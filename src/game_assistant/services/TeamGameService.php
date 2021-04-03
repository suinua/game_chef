<?php


namespace game_assistant\services;


use game_assistant\models\GameId;
use game_assistant\models\TeamGame;
use game_assistant\models\TeamId;
use game_assistant\store\GamesStore;
use game_assistant\store\PlayerDataStore;

class TeamGameService
{
    /**
     * @param string $name
     * @param GameId $gameId
     * @param TeamId|null $teamId
     * @param bool $force
     * @throws \Exception
     */
    static function join(string $name, GameId $gameId, ?TeamId $teamId = null, bool $force = false) {
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

        }

        //指定あり、強制
        if ($force) {

        //指定あり、非強制
        } else{

        }
    }
}