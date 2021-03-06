<?php


namespace game_chef\models\map_data;


use pocketmine\math\Vector3;

class TeamDataOnMap
{
    private string $name;
    private string $colorFormat;
    /**
     * @var Vector3[]
     */
    private array $spawnPoints;
    protected ?int $maxPlayers;
    protected ?int $minPlayers;
    /**
     * @var CustomTeamVectorData[]
     */
    private array $customTeamVectorDataList;
    /**
     * @var CustomTeamArrayVectorData[]
     */
    private array $customTeamArrayVectorDataList;

    public function __construct(string $name, string $colorFormat, array $spawnPoints, ?int $maxPlayers, ?int $minPlayers, array $customTeamVectorDataList, array $customTeamArrayVectorDataList) {
        $this->name = $name;
        $this->colorFormat = $colorFormat;
        $this->spawnPoints = $spawnPoints;
        $this->maxPlayers = $maxPlayers;
        $this->minPlayers = $minPlayers;
        $this->customTeamVectorDataList = $customTeamVectorDataList;
        $this->customTeamArrayVectorDataList = $customTeamArrayVectorDataList;
    }

    public function toJson(): array {
        $spawnPoints = [];
        foreach ($this->spawnPoints as $spawnPoint) {
            $spawnPoints[] = [
                "x" => $spawnPoint->getX(),
                "y" => $spawnPoint->getY(),
                "z" => $spawnPoint->getZ(),
            ];
        }

        $customTeamVectorDataList = [];
        foreach ($this->customTeamVectorDataList as $customTeamVectorData) {
            $customTeamVectorDataList[] = $customTeamVectorData->toJson();
        }

        $customTeamArrayVectorDataList = [];
        foreach ($this->customTeamArrayVectorDataList as $customTeamArrayVectorData) {
            $customTeamArrayVectorDataList[] = $customTeamArrayVectorData->toJson();
        }

        return [
            "name" => $this->name,
            "color_format" => $this->colorFormat,
            "spawn_points" => $spawnPoints,
            "max_players" => $this->maxPlayers,
            "min_players" => $this->maxPlayers,
            "custom_team_vector_data_list" => $customTeamVectorDataList,
            "custom_team_array_vector_data_list" => $customTeamArrayVectorDataList,
        ];
    }

    static function fromJson(array $json): self {
        $spawnPoints = [];
        foreach ($json["spawn_points"] as $spawnPoint) {
            $spawnPoints[] = new Vector3($spawnPoint["x"], $spawnPoint["y"], $spawnPoint["z"]);
        }

        $customTeamVectorDataList = [];
        foreach ($json["custom_team_vector_data_list"] as $customTeamVectorData) {
            $customTeamVectorDataList[] = CustomTeamVectorData::fromJson($customTeamVectorData);
        }

        $customTeamArrayVectorDataList = [];
        foreach ($json["custom_team_array_vector_data_list"] as $customTeamArrayVectorData) {
            $customTeamArrayVectorDataList[] = CustomTeamArrayVectorData::fromJson($customTeamArrayVectorData);
        }

        return new TeamDataOnMap($json["name"], $json["color_format"], $spawnPoints, $json["max_players"], $json["min_players"], $customTeamVectorDataList, $customTeamArrayVectorDataList);
    }

    public function getName(): string {
        return $this->name;
    }

    public function getColorFormat(): string {
        return $this->colorFormat;
    }

    /**
     * @return Vector3[]
     */
    public function getSpawnPoints(): array {
        return $this->spawnPoints;
    }

    public function getMaxPlayers(): ?int {
        return $this->maxPlayers;
    }

    public function getMinPlayers(): ?int {
        return $this->minPlayers;
    }

    /**
     * @return CustomTeamVectorData[]
     */
    public function getCustomTeamVectorDataList(): array {
        return $this->customTeamVectorDataList;
    }

    /**
     * @return CustomTeamArrayVectorData[]
     */
    public function getCustomTeamArrayVectorDataList(): array {
        return $this->customTeamArrayVectorDataList;
    }

    public function setMaxPlayers(?int $maxPlayers): void {
        $this->maxPlayers = $maxPlayers;
    }

    public function setMinPlayers(?int $minPlayers): void {
        $this->minPlayers = $minPlayers;
    }

    /**
     * @param Vector3 $vector3
     * @throws \Exception
     */
    public function addSpawnPoint(Vector3 $vector3) {
        foreach ($this->spawnPoints as $spawnPoint) {
            if ($spawnPoint->equals($vector3)) {
                throw new \Exception("TeamGameMap????????????????????????????????????????????????????????????????????????????????????????????????????????????");
            }
        }

        $this->spawnPoints[] = $vector3;
    }

    /**
     * @param Vector3 $vector3
     * @throws \Exception
     */
    public function deleteSpawnPoint(Vector3 $vector3) {
        $newSpawnPoints = [];
        $isExist = false;
        foreach ($this->spawnPoints as $spawnPoint) {
            if ($spawnPoint->equals($vector3)) {
                $isExist = true;
            } else {
                $newSpawnPoints[] = $spawnPoint;
            }
        }
        if (!$isExist) {
            throw new \Exception("????????????????????????????????????????????????????????????????????????");
        }

        $this->spawnPoints = array_values($newSpawnPoints);
    }


    /**
     * @param string $key
     * @return CustomTeamVectorData
     * @throws \Exception
     */
    public function getCustomVectorData(string $key): CustomTeamVectorData {
        foreach ($this->customTeamVectorDataList as $customTeamVectorData) {
            if ($customTeamVectorData->getKey() === $key) {
                return $customTeamVectorData;
            }
        }

        throw new \Exception("??????key({$key})????????????????????????????????????????????????????????????");
    }


    /**
     * @param CustomTeamVectorData $customTeamVectorData
     * @throws \Exception
     */
    public function addCustomVectorData(CustomTeamVectorData $customTeamVectorData) {
        foreach ($this->customTeamVectorDataList as $data) {
            if ($data->getKey() === $customTeamVectorData->getKey()) {
                throw new \Exception("??????Key??????????????????????????????????????????????????????????????????????????????");
            }
        }
        $this->customTeamVectorDataList[] = $customTeamVectorData;
    }

    /**
     * @param CustomTeamVectorData $customTeamVectorData
     * @throws \Exception
     */
    public function updateCustomVectorData(CustomTeamVectorData $customTeamVectorData) {
        $isExist = false;
        foreach ($this->customTeamVectorDataList as $index => $data) {
            if ($data->getKey() === $customTeamVectorData->getKey()) {
                $isExist = true;
                $this->customTeamVectorDataList[$index] = $customTeamVectorData;
            }
        }

        if (!$isExist) {
            throw new \Exception("??????????????????????????????????????????????????????????????????????????????????????????");
        }
    }

    /**
     * @param CustomTeamVectorData $target
     * @throws \Exception
     */
    public function deleteCustomVectorData(CustomTeamVectorData $target) {
        $isExist = false;
        $newList = [];
        foreach ($this->customTeamVectorDataList as $data) {
            if ($data->getKey() === $target->getKey()) {
                $isExist = true;
            } else {
                $newList[] = $data;
            }
        }

        if (!$isExist) {
            throw new \Exception("??????????????????????????????????????????????????????????????????????????????????????????");
        }

        $this->customTeamVectorDataList = array_values($newList);
    }

    /**
     * @param string $key
     * @return CustomTeamArrayVectorData
     * @throws \Exception
     */
    public function getCustomArrayVectorData(string $key): CustomTeamArrayVectorData {
        foreach ($this->customTeamArrayVectorDataList as $customTeamArrayVectorData) {
            if ($customTeamArrayVectorData->getKey() === $key) {
                return $customTeamArrayVectorData;
            }
        }

        throw new \Exception("??????key({$key})?????????????????????????????????????????????????????????????????????");
    }

    /**
     * @param CustomTeamArrayVectorData $customArrayTeamVectorData
     * @throws \Exception
     */
    public function addCustomArrayVectorData(CustomTeamArrayVectorData $customArrayTeamVectorData) {
        foreach ($this->customTeamArrayVectorDataList as $data) {
            if ($data->getKey() === $customArrayTeamVectorData->getKey()) {
                throw new \Exception("??????Key???????????????????????????????????????????????????????????????????????????????????????");
            }
        }
        $this->customTeamVectorDataList[] = $customArrayTeamVectorData;
    }

    /**
     * @param CustomTeamArrayVectorData $target
     * @throws \Exception
     */
    public function updateCustomArrayVectorData(CustomTeamArrayVectorData $target) {
        $isExist = false;
        foreach ($this->customTeamArrayVectorDataList as $index => $customTeamArrayVectorData) {
            if ($customTeamArrayVectorData->getKey() === $target->getKey()) {
                $isExist = true;
                $this->customTeamArrayVectorDataList[$index] = $target;
            }
        }

        if (!$isExist) {
            throw new \Exception("???????????????????????????????????????????????????????????????????????????????????????????????????");
        }
    }
}