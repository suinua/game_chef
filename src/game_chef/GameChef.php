<?php


namespace game_chef;


use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\Score;
use game_chef\models\FFAGame;
use game_chef\models\TeamGame;
use game_chef\models\TeamId;
use game_chef\services\GameService;
use game_chef\services\FFAGameService;
use game_chef\services\TeamGameService;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginLogger;
use pocketmine\scheduler\TaskScheduler;

class GameChef
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

    static function finishGame(GameId $gameId): bool {
        try {
            GameService::finish($gameId);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }
        return true;
    }

    static function joinFFAGame(string $name, GameId $gameId): bool {
        try {
            FFAGameService::join($name, $gameId);
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

    static function setTeamGamePlayerSpawnPoint(Player $player): bool {
        try {
            $position = TeamGameService::getRandomSpawnPoint($player->getName());
            $player->setSpawn($position);
        } catch (\Exception $e) {
            self::$logger->error($e);
            return false;
        }

        return true;
    }

    /**
     * @param Player $player
     */
    static function setFFAPlayerSpawnPoint(Player $player): bool {
        try {
            $position = FFAGameService::getRandomSpawnPoint($player->getName());
            $player->setSpawn($position);
        } catch (\Exception $e) {
            self::$logger->error($e);
            return false;
        }

        return true;
    }

    static function setGamePlayersSpawnPoint(): bool { }

    static function setTeamPlayersSpawnPoint(): bool { }

    static function addTeamScore(GameId $gameId, TeamId $teamId, Score $score): bool {
        try {
            $game = GamesStore::getById($gameId);
            if ($game instanceof TeamGame) {
                $game->addScore($teamId, $score);
            } else {
                self::$logger->error("そのゲームIDはTeamGameのものではありません");
                return false;
            }
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    static function addPlayerScore(GameId $gameId, string $name, Score $score): bool {
        try {
            $game = GamesStore::getById($gameId);
            if ($game instanceof FFAGame) {
                $game->addScore($name, $score);
            } else {
                self::$logger->error("そのゲームIDはFFAGameのものではありません");
                return false;
            }
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        return true;
    }
}