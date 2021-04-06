<?php


namespace game_chef\repository\dto;


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

            $teamDataList[] = [
                "name" => $teamDataOnMap->getTeamName(),
                "color_format" => $teamDataOnMap->getTeamColorFormat(),
                "max_players" => $teamDataOnMap->getMaxPlayer(),
                "min_players" => $teamDataOnMap->getMinPlayer(),
                "spawn_points" => $spawnPoints,
            ];
        }


        return [
            "name" => $gameMap->getName(),
            "level_name" => $gameMap->getLevelName(),
            "adapted_game_types" => array_map(fn(GameType $type) => strval($type), $gameMap->getAdaptedGameTypes()),
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
            foreach ($array["spawn_points"] as $spawnPointAsArray) {
                $spawnPoints[] = new Vector3($spawnPointAsArray["x"], $spawnPointAsArray["y"], $spawnPointAsArray["z"]);
            }

            $teamDataList[$value["name"]] = new TeamDataOnMap(
                $value["name"],
                $value["color_format"],
                $value["max_players"],
                $value["min_players"],
                $spawnPoints
            );
        }

        $adaptedGameTypes = [];
        foreach ($array["adapted_game_types"] as $gameTypeAsString) {
            $adaptedGameTypes[] = new GameType($gameTypeAsString);
        }

        return new TeamGameMap(
            $array["name"],
            $array["level_name"],
            $adaptedGameTypes,
            $teamDataList
        );
    }
}