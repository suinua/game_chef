<?php


namespace game_chef\repository;


use game_chef\DataFolderPath;
use game_chef\models\FFAGameMap;
use game_chef\repository\dto\FFAGameMapDTO;

class FFAGameMapRepository
{
    /**
     * @param FFAGameMap $ffaGameMap
     * @throws \Exception
     */
    static function add(FFAGameMap $ffaGameMap): void {
        if (file_exists(DataFolderPath::$ffaGameMaps . $ffaGameMap->getName() . ".json")) {
            throw new \Exception("すでにその名前({$ffaGameMap->getName()})のマップが存在しています");
        }

        $array = FFAGameMapDTO::encode($ffaGameMap);
        file_put_contents(DataFolderPath::$ffaGameMaps . $ffaGameMap->getName() . ".json", json_encode($array));
    }

    /**
     * @param FFAGameMap $ffaGameMap
     * @throws \Exception
     */
    static function update(FFAGameMap $ffaGameMap): void {
        if (!file_exists(DataFolderPath::$ffaGameMaps . $ffaGameMap->getName() . ".json")) {
            throw new \Exception("その名前({$ffaGameMap->getName()})のマップは存在しません");
        }

        $array = FFAGameMapDTO::encode($ffaGameMap);
        file_put_contents(DataFolderPath::$ffaGameMaps . $ffaGameMap->getName() . ".json", json_encode($array));
    }

    /**
     * @param string $mapName
     * @throws \Exception
     */
    static function delete(string $mapName): void {
        if (!file_exists(DataFolderPath::$ffaGameMaps . $mapName . ".json")) {
            throw new \Exception("その名前({$mapName})のマップは存在しません");
        }

        unlink(DataFolderPath::$ffaGameMaps . $mapName . ".json");
    }

    /**
     * @return FFAGameMap[]
     * @throws \Exception
     */
    static function loadAll(): array {
        $maps = [];
        $dh = opendir(DataFolderPath::$ffaGameMaps);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype(DataFolderPath::$ffaGameMaps . $fileName) === "file") {
                $data = json_decode(file_get_contents(DataFolderPath::$ffaGameMaps . $fileName), true);
                $maps[] = FFAGameMapDTO::decode($data);
            }
        }

        closedir($dh);

        return $maps;
    }

    /**
     * @param string $mapName
     * @return FFAGameMap
     * @throws \Exception
     */
    static function loadByName(string $mapName): FFAGameMap {
        if (!file_exists(DataFolderPath::$ffaGameMaps . $mapName . ".json")) {
            throw new \Exception("その名前({$mapName})のマップは存在しません");
        }

        $mapsData = json_decode(file_get_contents(DataFolderPath::$ffaGameMaps . $mapName . ".json"), true);
        return FFAGameMapDTO::decode($mapsData);
    }
}