<?php


namespace game_chef\services;


use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\GameTimer;
use game_chef\models\PlayerData;
use game_chef\pmmp\events\FinishedGameEvent;
use game_chef\store\GamesStore;
use game_chef\store\GameTimersStore;
use game_chef\store\PlayerDataStore;
use pocketmine\scheduler\TaskScheduler;

class GameService
{
    /**
     * @param Game $game
     * @throws \Exception
     */
    static function register(Game $game) {
        GamesStore::add($game);
        $timer = new GameTimer($game->getId(), $game->getTimeLimit());
        GameTimersStore::add($timer);
    }

    /**
     * @param GameId $gameId
     * @param TaskScheduler $scheduler
     * @throws \Exception
     */
    static function start(GameId $gameId, TaskScheduler $scheduler): void {
        $game = GamesStore::getById($gameId);
        $timer = GameTimersStore::getById($gameId);

        $game->start();
        $timer->start($scheduler);
    }

    /**
     * @param GameId $gameId
     * @throws \Exception
     */
    static function finish(GameId $gameId) {
        $timer = GameTimersStore::getById($gameId);
        $game = GamesStore::getById($gameId);

        $timer->stop();
        $game->finished();

        (new FinishedGameEvent($gameId))->call();

        //最後に実行
        GameTimersStore::delete($gameId);
        GamesStore::delete($gameId);
        foreach (PlayerDataStore::getByGameId($gameId) as $playerData) {
            PlayerDataStore::update(new PlayerData($playerData->getName()));
        }
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    static function quit(string $name) {
        PlayerDataStore::update(new PlayerData($name));
    }
}