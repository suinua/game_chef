<?php


namespace game_chef\models\map_data;


use game_chef\models\GameType;

class TeamGameMapData extends MapData
{
    /**
     * @var TeamDataOnMap[]
     */
    private array $teamDataList;

    private function __construct(string $name, string $levelName, array $adaptedGameTypes, array $customMapVectorDataList, array $customMapArrayVectorDataList, array $teamDataList) {
        parent::__construct($name, $levelName, $adaptedGameTypes, $customMapVectorDataList, $customMapArrayVectorDataList);
        $this->teamDataList = $teamDataList;
    }

    static function asNew(string $name, string $levelName, array $gameTypeList): TeamGameMapData {
        return new TeamGameMapData($name, $levelName, $gameTypeList, [], [], []);
    }

    public function toJson(): array {
        $customMapVectorDataList = [];
        foreach ($this->customMapVectorDataList as $customMapVectorData) {
            $customMapVectorDataList[] = $customMapVectorData->toJson();
        }

        $customMapArrayVectorDataList = [];
        foreach ($this->customMapArrayVectorDataList as $customMapArrayVectorData) {
            $customMapArrayVectorDataList[] = $customMapArrayVectorData->toJson();
        }

        $teamDataList = [];
        foreach ($this->teamDataList as $teamDataOnMap) {
            $teamDataList[] = $teamDataOnMap->toJson();
        }

        return [
            "name" => $this->name,
            "level_name" => $this->levelName,
            "adapted_game_types" => array_map(fn(GameType $type) => strval($type), $this->adaptedGameTypes),
            "custom_map_vector_data_list" => $customMapVectorDataList,
            "custom_map_array_vector_data_list" => $customMapArrayVectorDataList,
            "team_data_list" => $teamDataList,
        ];
    }

    static function fromJson(array $json): TeamGameMapData {
        $customMapVectorDataList = [];
        foreach ($json["custom_map_vector_data_list"] as $customMapVectorData) {
            $customMapVectorDataList[] = CustomMapVectorData::fromJson($customMapVectorData);
        }

        $customMapArrayVectorDataList = [];
        foreach ($json["custom_map_array_vector_data_list"] as $customMapArrayVectorData) {
            $customMapArrayVectorDataList[] = CustomMapArrayVectorData::fromJson($customMapArrayVectorData);
        }

        $teamDataList = [];
        foreach ($json["team_data_list"] as $teamData) {
            $teamDataList[] = TeamDataOnMap::fromJson($teamData);
        }


        return new TeamGameMapData(
            $json["name"],
            $json["level_name"],
            array_map(fn(string $type) => new GameType($type), $json["adapted_game_types"]),
            $customMapVectorDataList,
            $customMapArrayVectorDataList,
            $teamDataList
        );
    }

    /**
     * @return TeamDataOnMap[]
     */
    public function getTeamDataList(): array {
        return $this->teamDataList;
    }

    /**
     * @param string $teamName
     * @return TeamDataOnMap|mixed
     * @throws \Exception
     */
    public function getTeamData(string $teamName) {
        foreach ($this->teamDataList as $teamDataOnMap) {
            if ($teamDataOnMap->getName() === $teamName) {
                return $teamDataOnMap;
            }
        }

        throw  new \Exception("????????????????????????({$teamName})?????????????????????");
    }

    /**
     * @param TeamDataOnMap $teamDataOnMap
     * @throws \Exception
     */
    public function addTeamData(TeamDataOnMap $teamDataOnMap) {
        foreach ($this->teamDataList as $teamData) {
            if ($teamData->getName() === $teamDataOnMap->getName()) {
                throw  new \Exception("????????????????????????({$teamDataOnMap->getName()})???????????????????????????????????????");
            }
        }

        $this->teamDataList[] = $teamDataOnMap;
    }
}