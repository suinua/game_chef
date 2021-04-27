<?php


namespace game_chef\services;


use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\PlayerData;
use game_chef\models\Team;
use game_chef\models\TeamGame;
use game_chef\models\TeamId;
use game_chef\pmmp\events\PlayerJoinGameEvent;
use game_chef\pmmp\events\PlayerMoveTeamEvent;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use game_chef\utilities\SortTeamsByPlayers;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class TeamGameService
{
    /**
     * @param Player $player
     * @param GameId $gameId
     * @param TeamId|null $teamId
     * @param bool $force
     * @throws \Exception
     */
    static function join(Player $player, GameId $gameId, ?TeamId $teamId = null, bool $force = false): void {
        $playerData = PlayerDataStore::getByName($player->getName());
        $game = GamesStore::getById($gameId);

        if (!($game instanceof TeamGame)) {
            throw new \Exception("そのゲームIDはTeamGameのものではありません");
        }

        if (!$game->canJoin($playerData->getName())) {
            throw new \Exception("ゲームに参加するとこができませんでした");
        }

        $onSuccess = function (Player $player, Game $game, TeamId $teamId) {
            $event = new PlayerJoinGameEvent($player, $game->getId(), $game->getType(), $teamId);
            $event->call();
            if ($event->isCancelled()) return;

            $newPlayerData = new PlayerData($player->getName(), $game->getId(), $teamId);
            PlayerDataStore::update($newPlayerData);
        };

        //チーム指定なし
        if ($teamId === null) {
            $teams = $game->getTeams();
            $teams = SortTeamsByPlayers::sort($teams);

            $teamId = $teams[0]->getId();

            $onSuccess($player, $game, $teamId);
            return;
        }

        if ($force) {
            //指定あり、強制
            $onSuccess($player, $game, $teamId);
        } else {
            //指定あり、非強制
            $sortedTeams = SortTeamsByPlayers::sort($game->getTeams());

            //指定のチームに参加できるかどうか
            //人数制限
            $team = $game->getTeamById($teamId);
            if (count(PlayerDataStore::getByTeamId($teamId)) >= $team->getMaxPlayer()) {
                //TODO:これは別に例外ではない。ここで使うのは間違い
                throw new \Exception("人数制限の関係でチームには参加できません");
            }

            $desertedTeam = $sortedTeams[0];

            //人数差
            if ($desertedTeam->getId()->equals($teamId)) {
                //参加しようとしているチームが一番不人気なら
                $onSuccess($player, $game, $teamId);
            } else {
                $desertedTeamPlayers = PlayerDataStore::getByTeamId($desertedTeam->getId());
                $teamPlayers = PlayerDataStore::getByTeamId($teamId);

                if ($game->getMaxPlayersDifference() === null) {
                    //人数差制限なし
                    $onSuccess($player, $game, $teamId);
                } else if (count($teamPlayers) - count($desertedTeamPlayers) < $game->getMaxPlayersDifference()) {
                    //人数差制限クリア
                    $onSuccess($player, $game, $teamId);
                } else {
                    //人数差制限
                    //TODO:これは別に例外ではない。ここで使うのは間違い
                    throw new \Exception("人数差の関係でそのチームには参加できません");
                }
            }
        }
    }

    /**
     * @param Player $player
     * @param TeamId $teamId
     * @param bool $force
     * @throws \Exception
     */
    static function moveTeam(Player $player, TeamId $teamId, bool $force = false): void {
        $playerData = PlayerDataStore::getByName($player->getName());
        $oldTeamId = $playerData->getBelongTeamId();
        $gameId = $playerData->getBelongGameId();

        //参加していなかったらダメ
        if ($gameId === null) {
            throw new \Exception("試合に参加していないプレイヤーを、チーム移動させることはできません");
        }

        //TeamGameじゃなかったらダメ
        $game = GamesStore::getById($gameId);
        if (!($game instanceof TeamGame)) {
            throw new \Exception("そのゲームIDはTeamGameのものではありません");
        }

        //ゲームがチーム移動を許可してなかったらダメ
        if (!$game->isCanMoveTeam()) {
            throw new \Exception("そのゲームはチーム移動が許可されていません");
        }

        //すでに参加しているチームだったらダメ
        if ($playerData->getBelongTeamId()->equals($teamId)) {
            throw new \Exception("すでに参加しているチームに移動することはできません");
        }

        $onSuccess = function (Player $player, Game $game, TeamId $teamId, TeamId $oldTeamId) {
            $event = new PlayerMoveTeamEvent($player, $game->getId(), $game->getType(), $teamId, $oldTeamId);
            $event->call();
            if ($event->isCancelled()) return;

            PlayerDataStore::update(new PlayerData($player->getName(), $game->getId(), $teamId));
        };

        //TODO:joinとかぶるものが多いので、リファクタリング対象
        if ($force) {
            $onSuccess($player, $game, $teamId, $oldTeamId);
        } else {
            //指定あり、非強制
            $sortedTeams = SortTeamsByPlayers::sort($game->getTeams());

            //指定のチームに移動できるかどうか
            //人数制限
            $team = $game->getTeamById($teamId);
            if (count(PlayerDataStore::getByTeamId($teamId)) >= $team->getMaxPlayer()) {
                //TODO:これは別に例外ではない。ここで使うのは間違い
                throw new \Exception("人数制限の関係でチームに移動できません");
            }

            $desertedTeam = $sortedTeams[0];

            //人数差
            if ($desertedTeam->getId()->equals($teamId)) {
                //移動しようとしているチームが一番不人気なら
                $onSuccess($player, $game, $teamId, $oldTeamId);
            } else {
                $desertedTeamPlayers = PlayerDataStore::getByTeamId($desertedTeam->getId());
                $teamPlayers = PlayerDataStore::getByTeamId($teamId);

                if ($game->getMaxPlayersDifference() === null) {
                    //人数差制限なし
                    $onSuccess($player, $game, $teamId, $oldTeamId);
                } else if (count($teamPlayers) - count($desertedTeamPlayers) < $game->getMaxPlayersDifference()) {
                    //人数差制限クリア
                    $onSuccess($player, $game, $teamId, $oldTeamId);
                } else {
                    //人数差制限
                    //TODO:これは別に例外ではない。ここで使うのは間違い
                    throw new \Exception("人数差の関係でそのチームに移動できません");
                }
            }
        }
    }

    /**
     * @param string $playerName
     * @return Position
     * @throws \Exception
     */
    static function getRandomSpawnPoint(string $playerName): Position {
        $playerData = PlayerDataStore::getByName($playerName);
        if ($playerData->getBelongGameId() === null) {
            throw new \Exception("ゲームに参加していないプレイヤーのスポーン地点を取得することはできません");
        }

        $game = GamesStore::getById($playerData->getBelongGameId());
        if (!($game instanceof TeamGame)) {
            throw new \Exception("TeamGameに参加していないプレイヤーのスポーン地点を取得することはできません");
        }

        $team = $game->getTeamById($playerData->getBelongTeamId());

        $key = array_rand($team->getSpawnPoints());

        if ($key === null) {
            throw new \Exception("Map({$game->getMap()->getName()})のspawnPointsが設定されておらず空です");
        }

        if (is_numeric($key)) {
            $level = Server::getInstance()->getLevelByName($game->getMap()->getLevelName());
            return Position::fromObject($team->getSpawnPoints()[$key], $level);
        } else {
            throw new \Exception("spawnPointsのkeyに不正な値が入っています");
        }
    }
}