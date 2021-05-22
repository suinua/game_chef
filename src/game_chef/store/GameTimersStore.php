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

    static function add(GameTimer $timer): void {
        if (array_key_exists(strval($timer->getGameId()), self::$gameTimers)) {
            throw new \LogicException("すでに同じID({$timer->getGameId()})の試合のタイマー追加されています");
        }

        self::$gameTimers[strval($timer->getGameId())] = $timer;
    }

    static function delete(GameId $gameId): void {
        if (!array_key_exists(strval($gameId), self::$gameTimers)) {
            throw new \LogicException("存在しないID($gameId)のタイマーを削除することはできません");
        }

        unset(self::$gameTimers[strval($gameId)]);
    }

    static function getById(GameId $gameId): GameTimer {
        if (!array_key_exists(strval($gameId), self::$gameTimers)) {
            throw new \LogicException("そのタイマー($gameId)の試合は存在しません");
        }

        return self::$gameTimers[strval($gameId)];
    }
}