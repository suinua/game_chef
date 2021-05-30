<?php


namespace game_chef\services;


use game_chef\DataFolderPath;
use game_chef\models\FFAGameMap;
use game_chef\models\GameType;
use game_chef\models\TeamGameMap;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;

class MapService
{
    const GameChefWoldKey = "forGameChef";

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

    /**
     * @param string $name
     * @param GameType $gameType
     * @param int $numberOfTeams
     * @return TeamGameMap
     */
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

    static private function createInstantWorld(string $levelName): string {
        $uniqueLevelName = $levelName . uniqid() . self::GameChefWoldKey;
        self::copyWorld($levelName, $uniqueLevelName);

        return $uniqueLevelName;
    }

    static function copyWorld(string $folderName, string $newLevelName): void {
        self::copyFolder(DataFolderPath::$worlds . $folderName, DataFolderPath::$worlds . $newLevelName);
        self::fixLevelName($newLevelName);
        Server::getInstance()->loadLevel($newLevelName);
    }

    static private function copyFolder(string $dir, string $new_dir) {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $new_dir = rtrim($new_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // コピー元ディレクトリが存在すればコピーを行う
        if (is_dir($dir)) {
            // コピー先ディレクトリが存在しなければ作成する
            if (!is_dir($new_dir)) {
                mkdir($new_dir, 0777);
                chmod($new_dir, 0777);
            }

            // ディレクトリを開く
            if ($handle = opendir($dir)) {
                // ディレクトリ内のファイルを取得する
                while (false !== ($file = readdir($handle))) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    // 下の階層にディレクトリが存在する場合は再帰処理を行う
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

    static function deleteInstantWorld(string $levelName): void {
        if (strpos($levelName, self::GameChefWoldKey) === false) {
            throw new \LogicException("GameChefで生成されたワールド以外($levelName)を削除することはできません");
        }

        self::deleteWorld($levelName);
    }

    static function deleteWorld(string $levelName): void {
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

    static function isInstantWorld(string $levelName): bool {
        return strpos($levelName, self::GameChefWoldKey) !== false;
    }

    static function deleteAllInstantWorlds(): void {
        $path = DataFolderPath::$worlds;
        $files = scandir($path);
        foreach ($files as $file_name) {
            if (!preg_match('/^\.(.*)/', $file_name)) {
                if (self::isInstantWorld($file_name)) {
                    self::deleteInstantWorld($file_name);
                }
            }
        }
    }
}