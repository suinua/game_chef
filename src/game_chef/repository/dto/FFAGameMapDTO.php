<?php


namespace game_chef\repository\dto;


use game_chef\models\FFAGameMap;
use game_chef\models\GameType;
use pocketmine\math\Vector3;

class FFAGameMapDTO
{
    static function encode(FFAGameMap $gameMap): array {
        $spawnPoints = [];

        foreach ($gameMap->getSpawnPoints() as $spawnPoint) {
            $spawnPoints[] = [
                "x" => $spawnPoint->getX(),
                "y" => $spawnPoint->getY(),
                "z" => $spawnPoint->getZ(),
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
            "spawn_points" => $spawnPoints,
        ];
    }

    static function decode(array $array): FFAGameMap {
        $spawnPoints = [];
        foreach ($array["spawn_points"] as $spawnPointAsArray) {
            $spawnPoints[] = new Vector3($spawnPointAsArray["x"], $spawnPointAsArray["y"], $spawnPointAsArray["z"]);
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

        return new FFAGameMap(
            $array["name"],
            $array["level_name"],
            $adaptedGameTypes,
            $vectorDataList,
            $vectorsDataList,
            $spawnPoints
        );
    }
}