<?php


namespace game_chef\models;


use game_chef\models\map_data\TeamGameMapData;

class TeamGameMap extends Map
{
    public function __construct(string $name, string $levelName, array $customMapVectorDataList, array $customMapVectorsDataList) {
        parent::__construct($name, $levelName, $customMapVectorDataList, $customMapVectorsDataList);
    }

    static function fromMapData(TeamGameMapData $teamGameMapData): TeamGameMap {
        return new TeamGameMap(
            $teamGameMapData->getName(),
            $teamGameMapData->getLevelName(),
            $teamGameMapData->getCustomMapVectorDataList(),
            $teamGameMapData->getCustomMapArrayVectorDataList(),
        );
    }
}
