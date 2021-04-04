<?php


namespace game_assistant\services;


use game_assistant\models\GameId;
use game_assistant\models\PlayerData;
use game_assistant\models\TeamGame;
use game_assistant\models\TeamId;
use game_assistant\store\GamesStore;
use game_assistant\store\PlayerDataStore;
use game_assistant\utilities\SortTeamsByPlayers;

class TeamGameService
{
    /**
     * @param string $name
     * @param GameId $gameId
     * @param TeamId|null $teamId
     * @param bool $force
     * @throws \Exception
     */
    static function join(string $name, GameId $gameId, ?TeamId $teamId = null, bool $force = false): void {
        $playerData = PlayerDataStore::getByName($name);
        $game = GamesStore::getById($gameId);

        if (!($game instanceof TeamGame)) {
            throw new \Exception("そのゲームIDはTeamGameのものではありません");
        }

        if (!$game->canJoin($playerData->getName())) {
            //TODO:メッセージ
            return;
        }

        //チーム指定なし
        if ($teamId === null) {
            $teams = $game->getTeams();
            $teams = SortTeamsByPlayers::sort($teams);

            $teamId = $teams[0]->getId();

            $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $teamId);
            PlayerDataStore::update($newPlayerData);
            return;
        }

        if ($force) {
            //指定あり、強制
            $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $teamId);
            PlayerDataStore::update($newPlayerData);
        } else {
            //指定あり、非強制
            $sortedTeams = SortTeamsByPlayers::sort($game->getTeams());

            //指定のチームに参加できるかどうか
            //人数制限
            $team = $game->getTeamById($teamId);
            if (count(PlayerDataStore::getTeamPlayerData($teamId)) >= $team->getMaxPlayer()) {
                //TODO:これは別に例外ではない。ここで使うのは間違い
                throw new \Exception("人数制限の関係でチームには参加できません");
            }

            $desertedTeam = $sortedTeams[0];

            //人数差
            if ($desertedTeam->getId()->equals($teamId)) {
                //参加しようとしているチームが一番不人気なら
                $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $teamId);
                PlayerDataStore::update($newPlayerData);
            } else {
                $desertedTeamPlayers = PlayerDataStore::getTeamPlayerData($desertedTeam->getId());
                $teamPlayers = PlayerDataStore::getTeamPlayerData($teamId);

                if ($game->getMaxPlayersDifference() === null) {
                    //人数差制限なし
                    $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $teamId);
                    PlayerDataStore::update($newPlayerData);
                } else if (count($teamPlayers) - count($desertedTeamPlayers) < $game->getMaxPlayersDifference()) {
                    //人数差制限クリア
                    $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $teamId);
                    PlayerDataStore::update($newPlayerData);
                } else {
                    //人数差制限
                    //TODO:これは別に例外ではない。ここで使うのは間違い
                    throw new \Exception("人数差の関係でそのチームには参加できません");
                }
            }
        }
    }

    /**
     * @param string $name
     * @param TeamId $teamId
     * @param bool $force
     * @throws \Exception
     */
    static function moveTeam(string $name, TeamId $teamId, bool $force = false): void {
        $playerData = PlayerDataStore::getByName($name);
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

        //TODO:joinとかぶるものが多いので、リファクタリング対象
        if ($force) {
            PlayerDataStore::update(new PlayerData($name,));
        } else {
            //指定あり、非強制
            $sortedTeams = SortTeamsByPlayers::sort($game->getTeams());

            //指定のチームに移動できるかどうか
            //人数制限
            $team = $game->getTeamById($teamId);
            if (count(PlayerDataStore::getTeamPlayerData($teamId)) >= $team->getMaxPlayer()) {
                //TODO:これは別に例外ではない。ここで使うのは間違い
                throw new \Exception("人数制限の関係でチームに移動できません");
            }

            $desertedTeam = $sortedTeams[0];

            //人数差
            if ($desertedTeam->getId()->equals($teamId)) {
                //移動しようとしているチームが一番不人気なら
                PlayerDataStore::update(new PlayerData($playerData->getName(), $game->getId(), $teamId));
            } else {
                $desertedTeamPlayers = PlayerDataStore::getTeamPlayerData($desertedTeam->getId());
                $teamPlayers = PlayerDataStore::getTeamPlayerData($teamId);

                if ($game->getMaxPlayersDifference() === null) {
                    //人数差制限なし
                    $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $teamId);
                    PlayerDataStore::update($newPlayerData);
                } else if (count($teamPlayers) - count($desertedTeamPlayers) < $game->getMaxPlayersDifference()) {
                    //人数差制限クリア
                    $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $teamId);
                    PlayerDataStore::update($newPlayerData);
                } else {
                    //人数差制限
                    //TODO:これは別に例外ではない。ここで使うのは間違い
                    throw new \Exception("人数差の関係でそのチームに移動できません");
                }
            }
        }
    }
}