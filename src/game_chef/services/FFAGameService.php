<?php


namespace game_chef\services;


use game_chef\models\GameId;
use game_chef\models\PlayerData;
use game_chef\models\FFAGame;
use game_chef\models\FFAPlayerTeam;
use game_chef\pmmp\events\PlayerJoinGameEvent;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class FFAGameService
{
    /**
     * @param Player $player
     * @param GameId $gameId
     * @return bool
     * @throws \Exception
     */
    static function join(Player $player, GameId $gameId): bool {
        $name = $player->getName();
        $playerData = PlayerDataStore::getByName($name);
        $game = GamesStore::getById($gameId);

        if (!($game instanceof FFAGame)) {
            throw new \Exception("そのゲームIDはFFAGameのものではありません");
        }

        if (!$game->canJoin($playerData->getName())) {
            return false;
        }

        $ffaTeam = new FFAPlayerTeam($playerData->getName());//TODO:ColorFormat
        $newPlayerData = new PlayerData($playerData->getName(), $game->getId(), $ffaTeam->getId());

        $event = new PlayerJoinGameEvent($player, $game->getId(), $game->getType(), $ffaTeam->getId());
        $event->call();
        if ($event->isCancelled()) return false;

        $game->addFFATeam($ffaTeam);
        PlayerDataStore::update($newPlayerData);
        return true;
    }

    /**
     * @param $playerName
     * @return Position
     * @throws \Exception
     */
    static function getRandomSpawnPoint($playerName): Position {
        $playerData = PlayerDataStore::getByName($playerName);
        if ($playerData->getBelongGameId() === null) {
            throw new \Exception("ゲームに参加していないプレイヤーのスポーン地点を取得することはできません");
        }

        $game = GamesStore::getById($playerData->getBelongGameId());
        if (!($game instanceof FFAGame)) {
            throw new \Exception("FFAGameに参加していないプレイヤーのスポーン地点を取得することはできません");
        }

        $points = $game->getMap()->getSpawnPoints();
        $key = array_rand($points);

        if ($key === null) {
            throw new \Exception("Map({$game->getMap()->getName()})のspawnPointsが設定されておらず空です");
        }

        if (is_numeric($key)) {
            $level = Server::getInstance()->getLevelByName($game->getMap()->getLevelName());
            return Position::fromObject($points[$key], $level);
        } else {
            throw new \Exception("spawnPointsのkeyに不正な値が入っています");
        }
    }
}