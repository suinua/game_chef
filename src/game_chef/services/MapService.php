<?php


namespace game_chef\services;


use game_chef\DataFolderPath;
use game_chef\models\FFAGameMap;
use game_chef\models\GameType;
use game_chef\models\TeamGameMap;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;

class MapService
{
    const GameChefWoldKey = "forGameChef";//GameChefで生成された印
    const TemporaryWorldKey = "TemporaryWorld";//一時的なマップ

    /**
     * @param string $name
     * @param GameType $gameType
     * @param int|null $numberOfPlayers
     * @return FFAGameMap
     * numberOfPlayersを設定すると、スポーン地点よりプレイヤーが多い場合エラーを吐きます
     * 同じところにスポーンしていい場合を除き、設定することを推奨します
     */
    static function useFFAGameMap(string $name, GameType $gameType, ?int $numberOfPlayers = null): FFAGameMap {
        $mapData = FFAGameMapDataRepository::loadByName($name);
        if ($mapData->isAdaptedGameType($gameType)) {
            if ($numberOfPlayers !== null) {
                if ($mapData->getSpawnPoints() <= $numberOfPlayers) {
                    throw new \UnexpectedValueException("そのマップ({$name})はその人数({$numberOfPlayers})に対応していません");
                }
            }

            $uniqueLevelName = self::createInstantWorld($mapData->getLevelName());
            return FFAGameMap::fromMapData($mapData, $uniqueLevelName);
        } else {
            throw new \UnexpectedValueException("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
        }
    }

    static function useTeamGameMap(string $name, GameType $gameType, int $numberOfTeams): TeamGameMap {
        $mapData = TeamGameMapDataRepository::loadByName($name);
        if ($mapData->isAdaptedGameType($gameType)) {
            if ($numberOfTeams !== null) {
                //登録してあるチームデータより、多いチームすうはムリ
                if ($mapData->getTeamDataList() <= $numberOfTeams) {
                    throw new \UnexpectedValueException("そのマップ({$name})はそのチーム数({$numberOfTeams})に対応していません");
                }
            }

            $uniqueLevelName = self::createInstantWorld($mapData->getLevelName());
            return TeamGameMap::fromMapData($mapData, $uniqueLevelName);
        } else {
            throw new \UnexpectedValueException("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
        }
    }


    //試合で使うワールドを作成 LevelName + UUID
    static private function createInstantWorld(string $levelName): string {
        return self::copyWorld($levelName, $levelName . uniqid(), true);
    }

    //一時的なワールドを作成 LevelName + GameChefWorldKey + TemporaryWorldKey
    static function copyWorld(string $folderName, string $newLevelName, bool $isTemporary): string {
        $newLevelName .= self::GameChefWoldKey;
        if ($isTemporary) $newLevelName .= self::TemporaryWorldKey;

        self::copyFolder(DataFolderPath::$worlds . $folderName, DataFolderPath::$worlds . $newLevelName);
        self::fixLevelName($newLevelName);
        Server::getInstance()->loadLevel($newLevelName);

        return $newLevelName;
    }

    static function generateWorldName(string $newLevelName, bool $isTemporary): string {
        $newLevelName .= self::GameChefWoldKey;
        if ($isTemporary) $newLevelName .= self::TemporaryWorldKey;
        return $newLevelName;
    }

    static private function copyFolder(string $dir, string $new_dir) {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $new_dir = rtrim($new_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (is_dir($dir)) {
            if (!is_dir($new_dir)) {
                mkdir($new_dir, 0777);
                chmod($new_dir, 0777);
            }

            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    if (is_dir($dir . $file)) {
                        self::copyFolder($dir . $file, $new_dir . $file);
                    } else {
                        copy($dir . $file, $new_dir . $file);
                    }
                }
                closedir($handle);
            }
        }
    }

    static private function fixLevelName(string $levelName): void {
        $nbt = new BigEndianNBTStream();
        $levelData = $nbt->readCompressed(file_get_contents(DataFolderPath::$worlds . $levelName . DIRECTORY_SEPARATOR . "level.dat"));
        $levelData = $levelData->getCompoundTag("Data");
        $levelData->setString("LevelName", $levelName);
        $nbt = new BigEndianNBTStream();
        file_put_contents(DataFolderPath::$worlds . $levelName . DIRECTORY_SEPARATOR . "level.dat", $nbt->writeCompressed(new CompoundTag("", [$levelData])));
    }

    static function deleteWorld(string $levelName): void {
        if (strpos($levelName, self::GameChefWoldKey) === false) {
            throw new \LogicException("GameChefで生成されたワールド以外($levelName)を削除することはできません");
        }

        $server = Server::getInstance();
        $level = $server->getLevelByName($levelName);
        if ($level !== null) $server->unloadLevel($level);
        $path = DataFolderPath::$worlds . $levelName . DIRECTORY_SEPARATOR;
        self::deleteDir($path);
    }

    static private function deleteDir(string $path): void {
        $dh = opendir($path);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype($path . $fileName) === "file") {
                unlink($path . $fileName);
            } else if (!preg_match('/^\.(.*)/', $fileName)) {
                self::deleteDir($path . $fileName . DIRECTORY_SEPARATOR);
            }
        }

        closedir($dh);
        rmdir($path);
    }

    static function isTemporaryWorld(string $levelName): bool {
        return strpos($levelName, self::TemporaryWorldKey) !== false;
    }

    static function deleteAllTemporaryWorlds(): void {
        $path = DataFolderPath::$worlds;
        $files = scandir($path);
        foreach ($files as $file_name) {
            if (!preg_match('/^\.(.*)/', $file_name)) {
                if (self::isTemporaryWorld($file_name)) {
                    self::deleteWorld($file_name);
                }
            }
        }
    }

    static function getCopiedLevelByName(string $name, bool $isTemporary): Level {
        $name .= MapService::GameChefWoldKey;
        if ($isTemporary)  $name .= MapService::TemporaryWorldKey;
        return Server::getInstance()->getLevelByName($name);
    }
}