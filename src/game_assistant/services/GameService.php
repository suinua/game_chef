<?php


namespace game_assistant\services;


use game_assistant\events\FinishedGameEvent;
use game_assistant\models\Game;
use game_assistant\models\GameId;
use game_assistant\store\GamesStore;
use game_assistant\store\GameTimersStore;
use pocketmine\scheduler\TaskScheduler;

class GameService
{
    /**
     * @param Game $game
     * @throws \Exception
     */
    static function register(Game $game) {
        GamesStore::add($game);
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

    static function finish(GameId $gameId) {

        //最後に実行
        (new FinishedGameEvent($gameId))->call();
    }

    static function quit(string $name) { }
}