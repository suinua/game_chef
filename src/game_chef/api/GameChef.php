<?php


namespace game_chef\api;


use game_chef\models\FFAGameMap;
use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\PlayerData;
use game_chef\models\Score;
use game_chef\models\FFAGame;
use game_chef\models\TeamGame;
use game_chef\models\TeamGameMap;
use game_chef\models\TeamId;
use game_chef\services\GameService;
use game_chef\services\FFAGameService;
use game_chef\services\TeamGameService;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use pocketmine\Player;
use pocketmine\plugin\PluginLogger;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

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

    static function joinFFAGame(Player $player, GameId $gameId): bool {
        try {
            FFAGameService::join($player, $gameId);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }
        return true;
    }

    static function joinTeamGame(Player $player, GameId $gameId, ?TeamId $teamId = null, bool $force = false): bool {
        try {
            TeamGameService::join($player, $gameId, $teamId, $force);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }
        return true;
    }

    static function moveTeam(Player $player, TeamId $teamId, bool $force = false): bool {
        try {
            TeamGameService::moveTeam($player, $teamId, $force);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    static function quitGame(Player $player): bool {
        try {
            GameService::quit($player);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    static function setTeamGamePlayerSpawnPoint(Player $player): bool {
        if (!$player->isOnline()) {
            self::$logger->error("オフラインのプレイヤー({$player->getName()})のスポーン地点を設定することはできません");
            return false;
        }

        try {
            $position = TeamGameService::getRandomSpawnPoint($player->getName());
            $player->setSpawn($position);
        } catch (\Exception $e) {
            self::$logger->error($e);
            return false;
        }

        return true;
    }

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

    static function setFFAGamePlayersSpawnPoint(GameId $gameId): bool {
        $game = GamesStore::getById($gameId);
        if (!($game instanceof FFAGame)) {
            self::$logger->error("FFAGame以外のスポーン地点を取得することはできません");
            return false;
        }

        $indexList = array_rand($game->getMap()->getSpawnPoints(), $game->getTeams());

        foreach ($indexList as $key => $index) {
            $positions = $game->getMap()->getSpawnPoints()[$index];
            $player = Server::getInstance()->getPlayer($game->getTeams()[$key]->getName());
            if (!$player->isOnline()) {
                self::$logger->error("オフラインのプレイヤー({$player->getName()})のスポーン地点を設定することはできません");
                return false;
            }

            $player->setSpawn($positions[$index]);
        }

        return true;
    }

    static function setTeamPlayersSpawnPoint(GameId $gameId): bool {
        foreach (PlayerDataStore::getByGameId($gameId) as $playerData) {
            $player = Server::getInstance()->getPlayer($playerData->getName());
            $r = self::setTeamGamePlayerSpawnPoint($player);
            if ($r === false) {
                return false;
            }
        }

        return true;
    }

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

    static function getPlayerData(string $name): ?PlayerData {
        try {
            $playerData = PlayerDataStore::getByName($name);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return null;
        }

        return $playerData;
    }

    /**
     * @param GameId $gameId
     * @return PlayerData[]
     */
    static function getPlayerDataList(GameId $gameId): array {
        return PlayerDataStore::getByGameId($gameId);
    }

    /**
     * @param TeamId $teamId
     * @return PlayerData[]
     */
    static function getTeamPlayerDataList(TeamId $teamId): array {
        return PlayerDataStore::getByTeamId($teamId);
    }

    /**
     * @param GameId $gameId
     * @return FFAGame|TeamGame|null
     */
    static function findGameById(GameId $gameId): ?Game {
        try {
            $game = GamesStore::getById($gameId);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return null;
        }

        return $game;
    }

    static function findFFAGameById(GameId $gameId): ?FFAGame {
        $game = self::findGameById($gameId);
        if ($game instanceof FFAGame) {
            return $game;
        }
        return null;
    }

    static function findTeamGameById(GameId $gameId): ?TeamGame {
        $game = self::findGameById($gameId);
        if ($game instanceof TeamGame) {
            return $game;
        }
        return null;
    }

    static function getAvailableTeamGameMapNames(): array {

    }

    static function getAvailableFFAGameMapNames(): array {

    }
}