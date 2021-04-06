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


        return [
            "name" => $gameMap->getName(),
            "level_name" => $gameMap->getLevelName(),
            "adapted_game_types" => array_map(fn(GameType $type) => strval($type), $gameMap->getAdaptedGameTypes()),
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

        return new FFAGameMap(
            $array["name"],
            $array["level_name"],
            $adaptedGameTypes,
            $spawnPoints
        );
    }
}