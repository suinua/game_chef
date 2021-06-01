<?php


namespace game_chef\repository;


use game_chef\DataFolderPath;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\services\MapService;

class FFAGameMapDataRepository
{
    static function add(FFAGameMapData $ffaGameMapData): void {
        if (file_exists(DataFolderPath::$ffaGameMaps . $ffaGameMapData->getName() . ".json")) {
            throw new \LogicException("すでにその名前({$ffaGameMapData->getName()})のマップが存在しています");
        }

        if (MapService::isTemporaryWorld($ffaGameMapData->getLevelName())) {
            throw new \LogicException("コピーされた試合用のワールドで、マップを作成することはできません");
        }

        $json = $ffaGameMapData->toJson();
        file_put_contents(DataFolderPath::$ffaGameMaps . $ffaGameMapData->getName() . ".json", json_encode($json));
    }

    static function update(FFAGameMapData $ffaGameMapData): void {
        if (!file_exists(DataFolderPath::$ffaGameMaps . $ffaGameMapData->getName() . ".json")) {
            throw new \LogicException("存在しないマップ({$ffaGameMapData->getName()})を更新することはできません");
        }

        $json = $ffaGameMapData->toJson();
        file_put_contents(DataFolderPath::$ffaGameMaps . $ffaGameMapData->getName() . ".json", json_encode($json));
    }

    static function delete(string $mapName): void {
        if (!file_exists(DataFolderPath::$ffaGameMaps . $mapName . ".json")) {
            throw new \LogicException("存在しないマップ({$mapName})を削除することはできません");
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

    static function loadByName(string $mapName): FFAGameMapData {
        if (!file_exists(DataFolderPath::$ffaGameMaps . $mapName . ".json")) {
            throw new \LogicException("存在しないマップ({$mapName})を取得することはできません");
        }

        $json = json_decode(file_get_contents(DataFolderPath::$ffaGameMaps . $mapName . ".json"), true);
        return FFAGameMapData::fromJSON($json);
    }
}