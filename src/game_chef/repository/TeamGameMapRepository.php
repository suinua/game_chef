<?php


namespace game_chef\repository;


use game_chef\DataFolderPath;
use game_chef\models\TeamGameMap;
use game_chef\repository\dto\TeamGameMapDTO;

class TeamGameMapRepository
{
    /**
     * @param TeamGameMap $teamGameMap
     * @throws \Exception
     */
    static function add(TeamGameMap $teamGameMap): void {
        if (file_exists(DataFolderPath::$teamGameMaps . $teamGameMap->getName() . ".json")) {
            throw new \Exception("すでにその名前({$teamGameMap->getName()})のマップが存在しています");
        }

        $array = TeamGameMapDTO::encode($teamGameMap);
        file_put_contents(DataFolderPath::$teamGameMaps . $teamGameMap->getName() . ".json", json_encode($array));
    }

    /**
     * @param TeamGameMap $teamGameMap
     * @throws \Exception
     */
    static function update(TeamGameMap $teamGameMap): void {
        if (!file_exists(DataFolderPath::$teamGameMaps . $teamGameMap->getName() . ".json")) {
            throw new \Exception("その名前({$teamGameMap->getName()})のマップは存在しません");
        }

        $array = TeamGameMapDTO::encode($teamGameMap);
        file_put_contents(DataFolderPath::$teamGameMaps . $teamGameMap->getName() . ".json", json_encode($array));
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
     * @return array
     * @throws \Exception
     */
    static function loadAll(): array {
        $maps = [];
        $dh = opendir(DataFolderPath::$teamGameMaps);
        while (($fileName = readdir($dh)) !== false) {
            if (filetype(DataFolderPath::$teamGameMaps . $fileName) === "file") {
                $data = json_decode(file_get_contents(DataFolderPath::$teamGameMaps . $fileName), true);
                $maps[] = TeamGameMapDTO::decode($data);
            }
        }

        closedir($dh);

        return $maps;
    }

    /**
     * @param string $mapName
     * @return TeamGameMap
     * @throws \Exception
     */
    static function loadByName(string $mapName): TeamGameMap {
        if (!file_exists(DataFolderPath::$teamGameMaps . $mapName . ".json")) {
            throw new \Exception("その名前({$mapName})のマップは存在しません");
        }

        $mapsData = json_decode(file_get_contents(DataFolderPath::$teamGameMaps . $mapName . ".json"), true);
        return TeamGameMapDTO::decode($mapsData);
    }
}