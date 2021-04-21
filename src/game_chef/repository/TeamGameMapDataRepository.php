<?php


namespace game_chef\repository;


use game_chef\DataFolderPath;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\services\MapService;

class TeamGameMapDataRepository
{
    /**
     * @param TeamGameMapData $teamGameMapData
     * @throws \Exception
     */
    static function add(TeamGameMapData $teamGameMapData):void{
        if (file_exists(DataFolderPath::$teamGameMaps . $teamGameMapData->getName() . ".json")) {
            throw new \Exception("すでにその名前({$teamGameMapData->getName()})のマップが存在しています");
        }

        if (MapService::isInstantWorld($teamGameMapData->getLevelName())) {
            throw new \Exception("コピーされた試合用のワールドで、マップを作成することはできません");
        }

        $json = $teamGameMapData->toJson();
        file_put_contents(DataFolderPath::$teamGameMaps . $teamGameMapData->getName() . ".json", json_encode($json));
    }

    /**
     * @param TeamGameMapData $teamGameMapData
     * @throws \Exception
     */
    static function update(TeamGameMapData $teamGameMapData): void {
        if (!file_exists(DataFolderPath::$teamGameMaps . $teamGameMapData->getName() . ".json")) {
            throw new \Exception("その名前({$teamGameMapData->getName()})のマップは存在しません");
        }

        $json = $teamGameMapData->toJson();
        file_put_contents(DataFolderPath::$teamGameMaps . $teamGameMapData->getName() . ".json", json_encode($json));
    }

    /**
     * @param string $mapName
     * @throws \Exception
     */
    static function delete(string $mapName): void {
        if (!file_exists(DataFolderPath::$teamGameMaps . $mapName . ".json")) {
            throw new \Exception("その名前({$mapName})のマップは存在しません");
        }

        unlink(DataFolderPath::$teamGameMaps . $mapName . ".json");
    }

    /**
     * @return TeamGameMapData[]
     */
    static function loadAll(): array {
        $maps = [];
        $dh = opendir(DataFolderPath::$teamGameMaps);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype(DataFolderPath::$teamGameMaps . $fileName) === "file") {
                $json = json_decode(file_get_contents(DataFolderPath::$teamGameMaps . $fileName), true);
                $maps[] = TeamGameMapData::fromJson($json);
            }
        }
        closedir($dh);

        return $maps;
    }

    /**
     * @param string $mapName
     * @return TeamGameMapData
     * @throws \Exception
     */
    static function loadByName(string $mapName): TeamGameMapData {
        if (!file_exists(DataFolderPath::$teamGameMaps . $mapName . ".json")) {
            throw new \Exception("その名前({$mapName})のマップは存在しません");
        }

        $json = json_decode(file_get_contents(DataFolderPath::$teamGameMaps . $mapName . ".json"), true);
        return TeamGameMapData::fromJSON($json);
    }
}