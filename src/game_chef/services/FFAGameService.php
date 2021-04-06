<?php


namespace game_chef\services;


use game_chef\models\GameId;
use game_chef\models\PlayerData;
use game_chef\models\FFAGame;
use game_chef\models\FFAPlayerTeam;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;

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