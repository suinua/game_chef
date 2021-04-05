<?php


namespace game_assistant\services;


use game_assistant\models\GameId;
use game_assistant\models\PlayerData;
use game_assistant\models\FFAGame;
use game_assistant\models\FFAPlayerTeam;
use game_assistant\store\GamesStore;
use game_assistant\store\PlayerDataStore;

class FFAGameService
{
    /**
     * @param string $name
     * @param GameId $gameId
     * @throws \Exception
     */
    static function join(string $name, GameId $gameId) {
        $playerData = PlayerDataStore::getByName($name);
        $game = GamesStore::getById($gameId);

        if (!($game instanceof FFAGame)) {
            throw new \Exception("そのゲームIDはFFAGameのものではありません");
        }

        if (!$game->canJoin($playerData->getName())) {
            throw new \Exception("ゲームに参加するとこができませんでした");
        }

        $ffaTeam = new FFAPlayerTeam($playerData->getName());//TODO:ColorFormat
        $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $ffaTeam->getId());

        $game->addFFATeam($ffaTeam);
        PlayerDataStore::update($newPlayerData);
    }
}