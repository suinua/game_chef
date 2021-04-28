<?php


namespace game_chef\services;


use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\PlayerData;
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
    static function join(Player $player, GameId $gameId, ?TeamId $teamId = null, bool $force = false): bool {
        $playerData = PlayerDataStore::getByName($player->getName());
        $game = GamesStore::getById($gameId);

        if (!($game instanceof TeamGame)) {
            throw new \UnexpectedValueException("そのゲームIDはTeamGameのものではありません");
        }

        if (!$game->canJoin($playerData->getName())) {
            return false;
        }

        $onSuccess = function (Player $player, Game $game, TeamId $teamId): bool {
            $event = new PlayerJoinGameEvent($player, $game->getId(), $game->getType(), $teamId);
            $event->call();
            if ($event->isCancelled()) return false;

            $newPlayerData = new PlayerData($player->getName(), $game->getId(), $teamId);
            PlayerDataStore::update($newPlayerData);
            return true;
        };

        //チーム指定なし
        if ($teamId === null) {
            $teams = $game->getTeams();
            $teams = SortTeamsByPlayers::sort($teams);

            $teamId = $teams[0]->getId();

            return $onSuccess($player, $game, $teamId);
        }

        if ($force) {
            //指定あり、強制
            return $onSuccess($player, $game, $teamId);
        } else {
            //指定あり、非強制
            $sortedTeams = SortTeamsByPlayers::sort($game->getTeams());

            //指定のチームに参加できるかどうか
            //人数制限
            $team = $game->getTeamById($teamId);
            if (count(PlayerDataStore::getByTeamId($teamId)) >= $team->getMaxPlayer()) {
                return false;
            }

            $desertedTeam = $sortedTeams[0];

            //人数差
            if ($desertedTeam->getId()->equals($teamId)) {
                //参加しようとしているチームが一番不人気なら
                return $onSuccess($player, $game, $teamId);
            } else {
                $desertedTeamPlayers = PlayerDataStore::getByTeamId($desertedTeam->getId());
                $teamPlayers = PlayerDataStore::getByTeamId($teamId);

                if ($game->getMaxPlayersDifference() === null) {
                    //人数差制限なし
                    return $onSuccess($player, $game, $teamId);
                } else if (count($teamPlayers) - count($desertedTeamPlayers) < $game->getMaxPlayersDifference()) {
                    //人数差制限クリア
                    return $onSuccess($player, $game, $teamId);
                } else {
                    //人数差制限
                    return false;
                }
            }
        }
    }

    static function moveTeam(Player $player, TeamId $teamId, bool $force = false): bool {
        $playerData = PlayerDataStore::getByName($player->getName());
        $oldTeamId = $playerData->getBelongTeamId();
        $gameId = $playerData->getBelongGameId();

        //参加していなかったらダメ
        if ($gameId === null) {
            throw new \LogicException("試合に参加していないプレイヤーを、チーム移動させることはできません");
        }

        //TeamGameじゃなかったらダメ
        $game = GamesStore::getById($gameId);
        if (!($game instanceof TeamGame)) {
            throw new \UnexpectedValueException("そのゲームIDはTeamGameのものではありません");
        }

        //ゲームがチーム移動を許可してなかったらダメ
        if (!$game->isCanMoveTeam()) {
            throw new \LogicException("そのゲームはチーム移動が許可されていません");
        }

        //すでに参加しているチームだったらダメ
        if ($playerData->getBelongTeamId()->equals($teamId)) {
            throw new \LogicException("すでに参加しているチームに移動することはできません");
        }

        $onSuccess = function (Player $player, Game $game, TeamId $teamId, TeamId $oldTeamId): bool {
            $event = new PlayerMoveTeamEvent($player, $game->getId(), $game->getType(), $teamId, $oldTeamId);
            $event->call();
            if ($event->isCancelled()) return false;

            PlayerDataStore::update(new PlayerData($player->getName(), $game->getId(), $teamId));
            return true;
        };

        //TODO:joinとかぶるものが多いので、リファクタリング対象
        if ($force) {
            return $onSuccess($player, $game, $teamId, $oldTeamId);
        } else {
            //指定あり、非強制
            $sortedTeams = SortTeamsByPlayers::sort($game->getTeams());

            //指定のチームに移動できるかどうか
            //人数制限
            $team = $game->getTeamById($teamId);
            if (count(PlayerDataStore::getByTeamId($teamId)) >= $team->getMaxPlayer()) {
                return false;
            }

            $desertedTeam = $sortedTeams[0];

            //人数差
            if ($desertedTeam->getId()->equals($teamId)) {
                //移動しようとしているチームが一番不人気なら
                return $onSuccess($player, $game, $teamId, $oldTeamId);
            } else {
                $desertedTeamPlayers = PlayerDataStore::getByTeamId($desertedTeam->getId());
                $teamPlayers = PlayerDataStore::getByTeamId($teamId);

                if ($game->getMaxPlayersDifference() === null) {
                    //人数差制限なし
                    return $onSuccess($player, $game, $teamId, $oldTeamId);
                } else if (count($teamPlayers) - count($desertedTeamPlayers) < $game->getMaxPlayersDifference()) {
                    //人数差制限クリア
                    return $onSuccess($player, $game, $teamId, $oldTeamId);
                } else {
                    //人数差制限
                    return false;
                }
            }
        }
    }

    static function getRandomSpawnPoint(string $playerName): Position {
        $playerData = PlayerDataStore::getByName($playerName);
        if ($playerData->getBelongGameId() === null) {
            throw new \LogicException("ゲームに参加していないプレイヤーのスポーン地点を取得することはできません");
        }

        $game = GamesStore::getById($playerData->getBelongGameId());
        if (!($game instanceof TeamGame)) {
            throw new \LogicException("TeamGameに参加していないプレイヤーのスポーン地点を取得することはできません");
        }

        $team = $game->getTeamById($playerData->getBelongTeamId());

        $key = array_rand($team->getSpawnPoints());

        if ($key === null) {
            throw new \LogicException("Map({$game->getMap()->getName()})のspawnPointsが設定されておらず空です");
        }

        if (is_numeric($key)) {
            $level = Server::getInstance()->getLevelByName($game->getMap()->getLevelName());
            return Position::fromObject($team->getSpawnPoints()[$key]->add(0, 1, 0), $level);
        } else {
            throw new \LogicException("spawnPointsのkeyに不正な値が入っています");
        }
    }
}