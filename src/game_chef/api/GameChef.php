<?php


namespace game_chef\api;


use game_chef\models\FFAPlayerTeam;
use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\GameType;
use game_chef\models\PlayerData;
use game_chef\models\Score;
use game_chef\models\FFAGame;
use game_chef\models\Team;
use game_chef\models\TeamGame;
use game_chef\models\TeamId;
use game_chef\services\GameService;
use game_chef\services\FFAGameService;
use game_chef\services\TeamGameService;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use game_chef\utilities\SortFFATeamsByScore;
use game_chef\utilities\SortTeamsByScore;
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
            return FFAGameService::join($player, $gameId);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }
    }

    static function joinTeamGame(Player $player, GameId $gameId, ?TeamId $teamId = null, bool $force = false): bool {
        try {
            return TeamGameService::join($player, $gameId, $teamId, $force);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }
    }

    static function moveTeam(Player $player, TeamId $teamId, bool $force = false): bool {
        try {
            return TeamGameService::moveTeam($player, $teamId, $force);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }
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
        try {
            $game = GamesStore::getById($gameId);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

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

    static function setTeamGamePlayersSpawnPoint(GameId $gameId): bool {
        try {
            $game = GamesStore::getById($gameId);
        } catch (\Exception $e) {
            self::$logger->error($e->getMessage());
            return false;
        }

        if (!($game instanceof TeamGame)) {
            self::$logger->error("TeamGame以外のスポーン地点を取得することはできません");
            return false;
        }

        foreach (PlayerDataStore::getByGameId($gameId) as $playerData) {
            $player = Server::getInstance()->getPlayer($playerData->getName());
            $r = self::setTeamGamePlayerSpawnPoint($player);
            if ($r === false) {
                return false;
            }
        }

        return true;
    }

    static function addTeamGameScore(GameId $gameId, TeamId $teamId, Score $score): bool {
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

    static function addFFAGameScore(GameId $gameId, string $playerName, Score $score): bool {
        try {
            $game = GamesStore::getById($gameId);
            if ($game instanceof FFAGame) {
                $game->addScore($playerName, $score);
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

    /**
     * @param GameType $gameType
     * @return FFAGame[]|TeamGame[]
     */
    static function getGamesByType(GameType $gameType): array {
        return GamesStore::getByType($gameType);
    }

    static function getAllTeamGame(): array {
        return GamesStore::getAllTeamGame();
    }

    static function getAllFFAGame(): array {
        return GamesStore::getAllFFAGame();
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

    static function getTeamGameMapNamesByType(GameType $gameType): array {
    }


    static function getAvailableTeamGameMapNames(): array {
        //TODO:実装
    }

    static function getAvailableFFAGameMapNames(): array {
        //TODO:実装
    }

    static function isRelatedWith(Player $player, GameType $gameType): bool {
        try {
            $playerData = PlayerDataStore::getByName($player->getName());
        } catch (\Exception $e) {
            self::$logger->error($e);
            return false;
        }

        $gameId = $playerData->getBelongGameId();
        try {
            $game = GamesStore::getById($gameId);
        } catch (\Exception $e) {
            self::$logger->error($e);
            return false;
        }

        return $game->getType()->equals($gameType);
    }

    /**
     * @param Team[] $teams
     * @return Team[]
     */
    static function sortTeamsByScore(array $teams): array {
        return SortTeamsByScore::sort($teams);
    }

    /**
     * @param FFAPlayerTeam[] $teams
     * @return FFAPlayerTeam[]
     */
    static function sortFFATeamsByScore(array $teams): array {
        return SortFFATeamsByScore::sort($teams);
    }
}