<?php


namespace game_chef\repository\dto;


use game_chef\models\CustomTeamVectorData;
use game_chef\models\CustomTeamVectorsData;
use game_chef\models\GameType;
use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
use pocketmine\math\Vector3;

class TeamGameMapDTO
{
    static function encode(TeamGameMap $gameMap): array {
        $teamDataList = [];

        foreach ($gameMap->getTeamDataList() as $teamDataOnMap) {
            $spawnPoints = [];
            foreach ($teamDataOnMap->getSpawnPoints() as $spawnPoint) {
                $spawnPoints[] = [
                    "x" => $spawnPoint->getX(),
                    "y" => $spawnPoint->getY(),
                    "z" => $spawnPoint->getZ(),
                ];
            }

            $customTeamVectorDataList = [];
            foreach ($teamDataOnMap->getCustomTeamVectorDataList() as $customTeamVectorData) {
                $vector = $customTeamVectorData->getVector3();
                $customTeamVectorDataList[] = [
                    "key" => $customTeamVectorData->getKey(),
                    "team_name" => $customTeamVectorData->getTeamName(),
                    "x" => $vector->getX(),
                    "y" => $vector->getY(),
                    "z" => $vector->getZ()
                ];
            }

            $customTeamVectorsDataList = [];
            foreach ($teamDataOnMap->getCustomTeamVectorsDataList() as $customTeamVectorsData) {
                $vector3List = [];
                foreach ($customTeamVectorsData->getVector3List() as $vector3) {
                    $vector3List[] = [
                        "x" => $vector3->getX(),
                        "y" => $vector3->getY(),
                        "z" => $vector3->getZ()
                    ];
                }

                $customTeamVectorsDataList[] = [
                    "key" => $customTeamVectorsData->getKey(),
                    "team_name" => $customTeamVectorsData->getTeamName(),
                    "vector_list" => $vector3List
                ];
            }

            $teamDataList[] = [
                "name" => $teamDataOnMap->getTeamName(),
                "color_format" => $teamDataOnMap->getTeamColorFormat(),
                "max_players" => $teamDataOnMap->getMaxPlayer(),
                "min_players" => $teamDataOnMap->getMinPlayer(),
                "spawn_points" => $spawnPoints,
                "custom_team_vector_data_list" => $customTeamVectorDataList,
                "custom_team_vectors_data_list" => $customTeamVectorsDataList
            ];
        }

        $vectorDataList= [];
        foreach ($gameMap->getCustomMapVectorDataList() as $vectorData) {
            $vectorDataList[] = CustomMapVectorDataDTO::encodeVectorData($vectorData);
        }

        $vectorsDataList= [];
        foreach ($gameMap->getCustomMapVectorsDataList() as $vectorsData) {
            $vectorsDataList[] = CustomMapVectorDataDTO::encodeVectorsData($vectorsData);
        }


        return [
            "name" => $gameMap->getName(),
            "level_name" => $gameMap->getLevelName(),
            "adapted_game_types" => array_map(fn(GameType $type) => strval($type), $gameMap->getAdaptedGameTypes()),
            "custom_map_vector_data_list" => $vectorDataList,
            "custom_map_vectors_data_list" => $vectorsDataList,
            "team_data_list" => $teamDataList,
        ];
    }


    /**
     * @param array $array
     * @return TeamGameMap
     * @throws \Exception
     */
    static function decode(array $array): TeamGameMap {
        $teamDataList = [];
        foreach ($array["team_data_list"] as $value) {
            $spawnPoints = [];
            foreach ($value["spawn_points"] as $spawnPointAsArray) {
                $spawnPoints[] = new Vector3($spawnPointAsArray["x"], $spawnPointAsArray["y"], $spawnPointAsArray["z"]);
            }

            $customTeamVectorDataList = [];
            foreach ($value["custom_team_vector_data_list"] as $item) {
                $customTeamVectorDataList[] = new CustomTeamVectorData($item["key"], $item["team_name"], new Vector3($item["x"], $item["y"], $item["z"]));
            }

            $customTeamVectorsDataList = [];
            foreach ($value["custom_team_vectors_data_list"] as $item) {
                $vectors = [];
                foreach ($item["vector_list"] as $vectorAsArray) {
                    $vectors[] = new Vector3($vectorAsArray["x"], $vectorAsArray["y"], $vectorAsArray["z"]);
                }

                $customTeamVectorsDataList[] = new CustomTeamVectorsData(
                    $item["key"],
                    $item["team_name"],
                    $vectors
                );
            }

            $teamDataList[$value["name"]] = new TeamDataOnMap(
                $value["name"],
                $value["color_format"],
                $value["max_players"],
                $value["min_players"],
                $spawnPoints,
                $customTeamVectorDataList,
                $customTeamVectorsDataList
            );
        }

        $adaptedGameTypes = [];
        foreach ($array["adapted_game_types"] as $gameTypeAsString) {
            $adaptedGameTypes[] = new GameType($gameTypeAsString);
        }

        $vectorDataList= [];
        foreach ($array["custom_map_vector_data_list"] as $vectorDataAsArray) {
            $vectorDataList[] = CustomMapVectorDataDTO::decodeVectorData($vectorDataAsArray);
        }

        $vectorsDataList= [];
        foreach ($array["custom_map_vectors_data_list"] as $vectorsDataAsArray) {
            $vectorsDataList[] = CustomMapVectorDataDTO::decodeVectorsData($vectorsDataAsArray);
        }

        return new TeamGameMap(
            $array["name"],
            $array["level_name"],
            $adaptedGameTypes,
            $vectorDataList,
            $vectorsDataList,
            $teamDataList
        );
    }
}