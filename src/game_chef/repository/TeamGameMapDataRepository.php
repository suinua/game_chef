<?php


namespace game_chef\repository;


use game_chef\DataFolderPath;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\services\MapService;

class TeamGameMapDataRepository
{
    static function add(TeamGameMapData $teamGameMapData):void{
        if (file_exists(DataFolderPath::$teamGameMaps . $teamGameMapData->getName() . ".json")) {
            throw new \LogicException("すでにその名前({$teamGameMapData->getName()})のマップが存在しています");
        }

        if (MapService::isTemporaryWorld($teamGameMapData->getLevelName())) {
            throw new \LogicException("コピーされた試合用のワールドで、マップを作成することはできません");
        }

        $json = $teamGameMapData->toJson();
        file_put_contents(DataFolderPath::$teamGameMaps . $teamGameMapData->getName() . ".json", json_encode($json));
    }

    static function update(TeamGameMapData $teamGameMapData): void {
        if (!file_exists(DataFolderPath::$teamGameMaps . $teamGameMapData->getName() . ".json")) {
            throw new \LogicException("存在しないマップ({$teamGameMapData->getName()})を更新することはできません");
        }

        $json = $teamGameMapData->toJson();
        file_put_contents(DataFolderPath::$teamGameMaps . $teamGameMapData->getName() . ".json", json_encode($json));
    }

    static function delete(string $mapName): void {
        if (!file_exists(DataFolderPath::$teamGameMaps . $mapName . ".json")) {
            throw new \LogicException("存在しないマップ({$mapName})を削除することはできません");
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

    static function loadByName(string $mapName): TeamGameMapData {
        if (!file_exists(DataFolderPath::$teamGameMaps . $mapName . ".json")) {
            throw new \LogicException("存在しないマップを({$mapName})取得することはできません");
        }

        $json = json_decode(file_get_contents(DataFolderPath::$teamGameMaps . $mapName . ".json"), true);
        return TeamGameMapData::fromJSON($json);
    }
}