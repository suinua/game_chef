<?php


namespace game_chef\services;


use game_chef\DataFolderPath;
use game_chef\models\FFAGameMap;
use game_chef\models\GameType;
use game_chef\models\TeamGameMap;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\item\GoldenApple;
use pocketmine\item\ItemFactory;
use pocketmine\Server;

class MapService
{
    const GameChefWoldKey = "forGameChef";

    /**
     * @param string $name
     * @param GameType $gameType
     * @param int|null $numberOfPlayers
     * @return FFAGameMap
     * @throws \Exception numberOfPlayersを設定すると、スポーン地点よりプレイヤーが多い場合エラーを吐きます
     * 同じところにスポーンしていい場合を除き、設定することを推奨します
     */
    static function useFFAGameMap(string $name, GameType $gameType, ?int $numberOfPlayers = null): FFAGameMap {
        $mapData = FFAGameMapDataRepository::loadByName($name);
        if ($mapData->isAdaptedGameType($gameType)) {
            if ($numberOfPlayers !== null) {
                if ($mapData->getSpawnPoints() <= $numberOfPlayers) {
                    throw new \Exception("そのマップ({$name})はその人数({$numberOfPlayers})に対応していません");
                }
            }

            $uniqueLevelName = self::createInstantWorld($mapData->getLevelName());
            return FFAGameMap::fromMapData($mapData, $uniqueLevelName);
        } else {
            throw new \Exception("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
        }
    }

    /**
     * @param string $name
     * @param GameType $gameType
     * @param int $numberOfTeams
     * @return TeamGameMap
     * @throws \Exception
     */
    static function useTeamGameMap(string $name, GameType $gameType, int $numberOfTeams): TeamGameMap {
        $mapData = TeamGameMapDataRepository::loadByName($name);
        if ($mapData->isAdaptedGameType($gameType)) {
            if ($numberOfTeams !== null) {
                //登録してあるチームデータより、多いチームすうはムリ
                if ($mapData->getTeamDataList() <= $numberOfTeams) {
                    throw new \Exception("そのマップ({$name})はそのチーム数({$numberOfTeams})に対応していません");
                }
            }

            $uniqueLevelName = self::createInstantWorld($mapData->getLevelName());
            return TeamGameMap::fromMapData($mapData, $uniqueLevelName);
        } else {
            throw new \Exception("そのマップ({$name})はそのゲームタイプ({$gameType})に対応していません");
        }
    }

    static private function createInstantWorld(string $levelName): string {
        $uniqueLevelName = $levelName . uniqid() . self::GameChefWoldKey;
        self::copyFolder(DataFolderPath::$worlds . $levelName, DataFolderPath::$worlds . $uniqueLevelName);
        Server::getInstance()->loadLevel($uniqueLevelName);

        return $uniqueLevelName;
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

    /**
     * @param string $levelName
     * @throws \Exception
     */
    static function deleteInstantWorld(string $levelName): void {
        if (strpos($levelName, self::GameChefWoldKey) === false) {
            throw new \Exception("GameChefで生成されたワールド以外を削除することはできません");
        }

        $path = DataFolderPath::$worlds . $levelName;

        if (is_writable($path)) {
            $files = scandir($path);
            foreach ($files as $file_name) {
                if (!preg_match('/^\.(.*)/', $file_name)) {
                    unlink($path . $file_name);
                }
            }

            rmdir($path);
        }
    }

    static function isInstantWorld(string $levelName): bool {
        return strpos($levelName, self::GameChefWoldKey) !== false;
    }
}