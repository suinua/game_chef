<?php


namespace game_chef\store;


use game_chef\models\GameId;
use game_chef\models\GameTimer;

class GameTimersStore
{
    /**
     * @var GameTimer[]
     * GameId(string) => GameTimer
     */
    static private array $gameTimers = [];

    /**
     * @param GameTimer $timer
     * @throws \Exception
     */
    static function add(GameTimer $timer): void {
        if (array_key_exists(strval($timer->getGameId()), self::$gameTimers)) {
            throw new \Exception("すでに同じIDの試合のタイマー追加されています");
        }

        self::$gameTimers[strval($timer->getGameId())] = $timer;
    }

    /**
     * @param GameId $gameId
     * @throws \Exception
     */
    static function delete(GameId $gameId): void {
        if (!array_key_exists(strval($gameId), self::$gameTimers)) {
            throw new \Exception("そのIDの試合のタイマーは存在しません");
        }

        unset(self::$gameTimers[strval($gameId)]);
    }

    /**
     * @param GameId $gameId
     * @return GameTimer|null
     * @throws \Exception
     */
    static function getById(GameId $gameId): ?GameTimer {
        if (!array_key_exists(strval($gameId), self::$gameTimers)) {
            throw new \Exception("そのIDの試合のタイマーは存在しません");
        }

        return self::$gameTimers[strval($gameId)];
    }
}