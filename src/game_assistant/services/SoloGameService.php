<?php


namespace game_assistant\services;


use game_assistant\models\GameId;
use game_assistant\models\PlayerData;
use game_assistant\models\SoloGame;
use game_assistant\models\SoloTeam;
use game_assistant\store\GamesStore;
use game_assistant\store\PlayerDataStore;

class SoloGameService
{
    /**
     * @param string $name
     * @param GameId $gameId
     * @throws \Exception
     */
    static function join(string $name, GameId $gameId) {
        $playerData = PlayerDataStore::getByName($name);
        $game = GamesStore::getById($gameId);

        if (!($game instanceof SoloGame)) {
            throw new \Exception("そのゲームIDはSoloGameのものではありません");
        }

        if (!$game->canJoin($playerData->getName())) {
            throw new \Exception("ゲームに参加するとこができませんでした");
        }

        $soloTeam = new SoloTeam($playerData->getName());//TODO:ColorFormat
        $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $soloTeam->getId());

        $game->addSoloTeam($soloTeam);
        PlayerDataStore::update($newPlayerData);
    }
}