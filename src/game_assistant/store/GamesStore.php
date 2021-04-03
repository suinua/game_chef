<?php


namespace game_assistant\store;


use game_assistant\models\Game;
use game_assistant\models\GameId;

class GamesStore
{
    /**
     * @var Game[]
     * GameId(string) => Game
     */
    static private array $games = [];

    /**
     * @param Game $game
     * @throws \Exception
     */
    static function add(Game $game): void {
        if (array_key_exists(strval($game->getId()), self::$games)) {
            throw new \Exception("すでに同じIDの試合が追加されています");
        }

        self::$games[strval($game->getId())] = $game;
    }

    /**
     * @param GameId $gameId
     * @throws \Exception
     */
    static function delete(GameId $gameId): void {
        if (!array_key_exists(strval($gameId), self::$games)) {
            throw new \Exception("そのIDの試合は存在しません");
        }

        unset(self::$games[strval($gameId)]);
    }

    /**
     * @param GameId $gameId
     * @return Game
     * @throws \Exception
     */
    static function getById(GameId $gameId): Game {
        if (!array_key_exists(strval($gameId), self::$games)) {
            throw new \Exception("そのIDの試合は存在しません");
        }

        return self::$games[strval($gameId)];
    }
}