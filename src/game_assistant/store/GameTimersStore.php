<?php


namespace game_assistant\store;


use game_assistant\models\GameId;
use game_assistant\models\GameTimer;

class GameTimersStore
{
    /**
     * @var GameTimer[]
     * GameId(string) => GameTimer
     */
    static array $gameTimers = [];

    static function add(GameTimer $timer): void {
        if (array_key_exists(strval($timer->getGameId()), self::$gameTimers)) {
            throw new \Exception("すでに同じIDの試合のタイマー追加されています");
        }

        self::$gameTimers[strval($timer->getGameId())] = $timer;
    }

    static function delete(GameId $gameId): void {
        if (!array_key_exists(strval($gameId), self::$gameTimers)) {
            throw new \Exception("そのIDの試合のタイマーは存在しません");
        }

        unset(self::$gameTimers[strval($gameId)]);
    }

    static function getById(GameId $gameId): ?GameTimer {
        if (!array_key_exists(strval($gameId), self::$gameTimers)) {
            throw new \Exception("そのIDの試合のタイマーは存在しません");
        }

        return self::$gameTimers[strval($gameId)];
    }
}