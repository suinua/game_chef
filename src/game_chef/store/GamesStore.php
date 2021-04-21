<?php


namespace game_chef\store;


use game_chef\models\FFAGame;
use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\GameType;
use game_chef\models\TeamGame;

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

    static function getAll(): array {
        return self::$games;
    }

    static function getAllTeamGame(): array {
        $result = [];
        foreach (self::$games as $game) {
            if ($game instanceof TeamGame) {
                $result[] = $game;
            }
        }

        return $result;
    }

    static function getAllFFAGame(): array {
        $result = [];
        foreach (self::$games as $game) {
            if ($game instanceof FFAGame) {
                $result[] = $game;
            }
        }

        return $result;
    }

    /**
     * @param GameId $gameId
     * @return TeamGame|FFAGame
     * @throws \Exception
     */
    static function getById(GameId $gameId): Game {
        if (!array_key_exists(strval($gameId), self::$games)) {
            throw new \Exception("そのIDの試合は存在しません");
        }

        return self::$games[strval($gameId)];
    }

    /**
     * @param GameType $type
     * @return Game[]
     */
    static function getByType(GameType $type): array {
        $result = [];
        foreach (self::$games as $game) {
            if ($game->getType()->equals($type)) {
                $result[] = $game;
            }
        }

        return $result;
    }
}