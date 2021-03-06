<?php


namespace game_chef\api;


use game_chef\models\FFAPlayerTeam;
use game_chef\models\Game;
use game_chef\models\GameId;
use game_chef\models\GameStatus;
use game_chef\models\GameTimer;
use game_chef\models\GameType;
use game_chef\models\PlayerData;
use game_chef\models\Score;
use game_chef\models\FFAGame;
use game_chef\models\Team;
use game_chef\models\TeamGame;
use game_chef\models\TeamId;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\services\GameService;
use game_chef\services\FFAGameService;
use game_chef\services\MapService;
use game_chef\services\TeamGameService;
use game_chef\store\GamesStore;
use game_chef\store\GameTimersStore;
use game_chef\store\PlayerDataStore;
use game_chef\utilities\SortFFATeamsByScore;
use game_chef\utilities\SortTeamsByScore;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
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

    static function registerGame(Game $game): void {
        GameService::register($game);
    }

    static function startGame(GameId $gameId): void {
        GameService::start($gameId, self::$scheduler);
    }

    static function finishGame(GameId $gameId): void {
        GameService::finish($gameId);
    }

    static function discardGame(GameId $gameId): void {
        GameService::discard($gameId);
    }


    static function joinFFAGame(Player $player, GameId $gameId): bool {
        return FFAGameService::join($player, $gameId);
    }

    static function joinTeamGame(Player $player, GameId $gameId, ?TeamId $teamId = null, bool $force = false): bool {
        return TeamGameService::join($player, $gameId, $teamId, $force);
    }

    static function moveTeam(Player $player, TeamId $teamId, bool $force = false): bool {
        return TeamGameService::moveTeam($player, $teamId, $force);
    }

    static function quitGame(Player $player): void {
        GameService::quit($player);
    }

    static function setTeamGamePlayerSpawnPoint(Player $player): void {
        if (!$player->isOnline()) {
            throw new \LogicException("?????????????????????????????????({$player->getName()})????????????????????????????????????????????????????????????");
        }

        $position = TeamGameService::getRandomSpawnPoint($player->getName());
        $player->setSpawn($position);
    }

    static function setFFAPlayerSpawnPoint(Player $player): void {
        $position = FFAGameService::getRandomSpawnPoint($player->getName());
        $player->setSpawn($position);
    }

    static function setFFAPlayersSpawnPoint(GameId $gameId): void {
        $game = GamesStore::getById($gameId);

        if (!($game instanceof FFAGame)) {
            throw new \LogicException("FFAGame??????????????????????????????????????????????????????????????????");
        }

        $level = Server::getInstance()->getLevelByName($game->getMap()->getLevelName());
        $indexList = array_rand($game->getMap()->getSpawnPoints(), count($game->getTeams()));
        foreach ($indexList as $key => $index) {
            $vector3 = $game->getMap()->getSpawnPoints()[$index]->add(0, 1, 0);
            $player = Server::getInstance()->getPlayer($game->getTeams()[$key]->getName());
            if (!$player->isOnline()) {
                throw new \LogicException("?????????????????????????????????({$player->getName()})????????????????????????????????????????????????????????????");
            }

            $player->setSpawn(Position::fromObject($vector3, $level));
        }
    }

    static function setTeamGamePlayersSpawnPoint(GameId $gameId): void {
        $game = GamesStore::getById($gameId);

        if (!($game instanceof TeamGame)) {
            throw new \LogicException("TeamGame??????????????????????????????????????????????????????????????????");
        }

        foreach (PlayerDataStore::getByGameId($gameId) as $playerData) {
            $player = Server::getInstance()->getPlayer($playerData->getName());
            self::setTeamGamePlayerSpawnPoint($player);
        }
    }

    static function addTeamGameScore(GameId $gameId, TeamId $teamId, Score $score): void {
        $game = GamesStore::getById($gameId);
        if ($game->getStatus()->equals(GameStatus::Finished())) return;

        if ($game instanceof TeamGame) {
            $game->addScore($teamId, $score);
        } else {
            throw new \UnexpectedValueException("???????????????ID???TeamGame??????????????????????????????");
        }
    }

    static function addFFAGameScore(GameId $gameId, string $playerName, Score $score): void {
        $game = GamesStore::getById($gameId);
        if ($game->getStatus()->equals(GameStatus::Finished())) return;

        if ($game instanceof FFAGame) {
            $game->addScore($playerName, $score);
        } else {
            throw new \UnexpectedValueException("???????????????ID???FFAGame??????????????????????????????");
        }
    }

    static function findPlayerData(string $name): ?PlayerData {
        return PlayerDataStore::findByName($name);
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
        return GamesStore::findById($gameId);
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
        $game = GamesStore::findById($gameId);
        if ($game instanceof FFAGame) {
            return $game;
        }
        return null;
    }

    static function findTeamGameById(GameId $gameId): ?TeamGame {
        $game = GamesStore::findById($gameId);
        if ($game instanceof TeamGame) {
            return $game;
        }
        return null;
    }

    static function getTeamGameMapNamesByType(GameType $gameType): array {
        $names = [];
        foreach (TeamGameMapDataRepository::loadAll() as $mapData) {
            $types = array_map(fn(GameType $type) => strval($type), $mapData->getAdaptedGameTypes());
            if (in_array(strval($gameType), $types)) {
                $names[] = $mapData->getName();
            }
        }

        return $names;
    }

    static function getFFAGameMapNamesByType(GameType $gameType): array {
        $names = [];
        foreach (FFAGameMapDataRepository::loadAll() as $mapData) {
            $types = array_map(fn(GameType $type) => strval($type), $mapData->getAdaptedGameTypes());
            if (in_array(strval($gameType), $types)) {
                $names[] = $mapData->getName();
            }
        }

        return $names;
    }


    static function getTeamGameMapNames(): array {
        $names = [];
        foreach (TeamGameMapDataRepository::loadAll() as $mapData) {
            $names[] = $mapData->getName();
        }

        return $names;
    }

    static function getFFAGameMapNames(): array {
        $names = [];
        foreach (FFAGameMapDataRepository::loadAll() as $mapData) {
            $names[] = $mapData->getName();
        }

        return $names;
    }

    static function isRelatedWith(Player $player, GameType $gameType): bool {
        $playerData = PlayerDataStore::getByName($player->getName());
        $gameId = $playerData->getBelongGameId();
        if ($gameId === null) return false;

        $game = GamesStore::getById($gameId);
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

    static function findGameTimer(GameId $gameId): ?GameTimer {
        return GameTimersStore::getById($gameId);
    }

    /**
     * @param GameId $gameId
     * @param TeamId[] $allyTeamIds
     * @param Vector3 $center
     * @param float $distance
     * @return Player[]
     */
    static function getAroundTeamPlayers(GameId $gameId, array $allyTeamIds, Vector3 $center, float $distance): array {
        $teamIds = array_map(fn(TeamId $teamId) => strval($teamId), $allyTeamIds);

        $players = [];
        $map = GamesStore::getById($gameId)->getMap();
        $level = Server::getInstance()->getLevelByName($map->getLevelName());
        foreach ($level->getPlayers() as $player) {
            if ($center->distance($player->asVector3()) > $distance) continue;
            $playerData = PlayerDataStore::getByName($player->getName());

            //??????????????????????????????
            if ($playerData->getBelongGameId() === null) continue;
            if (!$playerData->getBelongGameId()->equals($gameId)) continue;

            $teamId = $playerData->getBelongTeamId();
            if (in_array($teamId, $teamIds)) {
                $players[] = $player;
            }
        }

        return $players;
    }

    /**
     * @param GameId $gameId
     * @param TeamId[] $allyTeamIds
     * @param Vector3 $center
     * @param float $distance
     * @return Player[]
     */
    static function getAroundEnemies(GameId $gameId, array $allyTeamIds, Vector3 $center, float $distance): array {
        $teamIds = array_map(fn(TeamId $teamId) => strval($teamId), $allyTeamIds);

        $players = [];
        $map = GamesStore::getById($gameId)->getMap();
        $level = Server::getInstance()->getLevelByName($map->getLevelName());
        foreach ($level->getPlayers() as $player) {
            if ($center->distance($player->asVector3()) > $distance) continue;
            $playerData = PlayerDataStore::getByName($player->getName());

            //??????????????????????????????
            if ($playerData->getBelongGameId() === null) continue;
            if (!$playerData->getBelongGameId()->equals($gameId)) continue;

            $teamId = $playerData->getBelongTeamId();
            if (!in_array($teamId, $teamIds)) {
                $players[] = $player;
            }
        }

        return $players;
    }

    static function copyWorld(string $levelName, string $newLevelName, bool $isTemporary = true): void {
        MapService::copyWorld($levelName, $newLevelName, $isTemporary);
    }

    static function deleteWorld(string $levelName): void {
        MapService::deleteWorld($levelName);
    }

    //$name??? ????????????????????????????????????Level???+???????????????uniqId???
    //?????????????????????????????????????????????????????????Level?????????
    static function getCopiedLevelByName(string $name, bool $isTemporary): Level {
        return MapService::getCopiedLevelByName($name, $isTemporary);
    }
}