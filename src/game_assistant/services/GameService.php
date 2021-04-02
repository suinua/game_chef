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
    static function register(Game $game) {

    }

    static function start(GameId $gameId, TaskScheduler $scheduler): void {
        try {
            $game = GamesStore::getById($gameId);
            $timer = GameTimersStore::getById($gameId);

            $game->start();
        } catch (\Exception $exception) {
            //TODO:メッセージ
            return;
        }

        $timer->start($scheduler);
    }

    static function finish(GameId $gameId) {

        //最後に実行
        (new FinishedGameEvent($gameId))->call();
    }

    static function quit() { }

    static function join() { }
}