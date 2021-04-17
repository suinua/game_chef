<?php


namespace game_chef\repository;


use game_chef\DataFolderPath;
use game_chef\models\map_data\FFAGameMapData;

class FFAGameMapDataRepository
{
    /**
     * @param FFAGameMapData $ffaGameMapData
     * @throws \Exception
     */
    static function add(FFAGameMapData $ffaGameMapData): void {
        if (file_exists(DataFolderPath::$ffaGameMaps . $ffaGameMapData->getName() . ".json")) {
            throw new \Exception("すでにその名前({$ffaGameMapData->getName()})のマップが存在しています");
        }

        $json = $ffaGameMapData->toJson();
        file_put_contents(DataFolderPath::$ffaGameMaps . $ffaGameMapData->getName() . ".json", json_encode($json));
    }

    /**
     * @param FFAGameMapData $ffaGameMapData
     * @throws \Exception
     */
    static function update(FFAGameMapData $ffaGameMapData): void {
        if (!file_exists(DataFolderPath::$ffaGameMaps . $ffaGameMapData->getName() . ".json")) {
            throw new \Exception("その名前({$ffaGameMapData->getName()})のマップは存在しません");
        }

        $json = $ffaGameMapData->toJson();
        file_put_contents(DataFolderPath::$ffaGameMaps . $ffaGameMapData->getName() . ".json", json_encode($json));
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
     * @return FFAGameMapData[]
     */
    static function loadAll(): array {
        $maps = [];
        $dh = opendir(DataFolderPath::$ffaGameMaps);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype(DataFolderPath::$ffaGameMaps . $fileName) === "file") {
                $json = json_decode(file_get_contents(DataFolderPath::$ffaGameMaps . $fileName), true);
                $maps[] = FFAGameMapData::fromJson($json);
            }
        }
        closedir($dh);

        return $maps;
    }

    /**
     * @param string $mapName
     * @return FFAGameMapData
     * @throws \Exception
     */
    static function loadByName(string $mapName): FFAGameMapData {
        if (!file_exists(DataFolderPath::$ffaGameMaps . $mapName . ".json")) {
            throw new \Exception("その名前({$mapName})のマップは存在しません");
        }

        $json = json_decode(file_get_contents(DataFolderPath::$ffaGameMaps . $mapName . ".json"), true);
        return FFAGameMapData::fromJSON($json);
    }
}