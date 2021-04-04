<?php


namespace game_assistant;


use game_assistant\models\Game;
use game_assistant\models\GameId;
use game_assistant\models\TeamId;
use game_assistant\services\GameService;
use game_assistant\services\SoloGameService;
use game_assistant\services\TeamGameService;
use pocketmine\plugin\PluginLogger;
use pocketmine\scheduler\TaskScheduler;

class GameAssistant
{
    static private PluginLogger $logger;
    static private TaskScheduler $scheduler;

    static function setLogger(PluginLogger $logger): void {
        self::$logger = $logger;
    }

    public static function setScheduler(TaskScheduler $scheduler): void {
        self::$scheduler = $scheduler;
    }

    static function registerGame(Game $game): bool {
        try {
            GameService::register($game);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    static function startGame(GameId $gameId): bool {
        try {
            GameService::start($gameId, self::$scheduler);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    static function finishGame(GameId $gameId): void {
        GameService::finish($gameId);
    }

    static function joinSoloGame(string $name, GameId $gameId): bool {
        try {
            SoloGameService::join($name, $gameId);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }
        return true;
    }

    static function joinTeamGame(string $name, GameId $gameId, ?TeamId $teamId = null, bool $force = false): bool {
        try {
            TeamGameService::join($name, $gameId, $teamId, $force);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }
        return true;
    }

    static function moveTeam(string $name, TeamId $teamId, bool $force = false): bool {
        try {
            TeamGameService::moveTeam($name, $teamId, $force);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    static function quitGame(string $name): bool {
        try {
            GameService::quit($name);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    static function setSpawnPoint(): bool { }

    static function setGamePlayersSpawnPoint(): bool { }

    static function setTeamPlayersSpawnPoint(): bool { }

    static function addTeamScore(): bool { }

    static function addPlayerScore(): bool { }
}