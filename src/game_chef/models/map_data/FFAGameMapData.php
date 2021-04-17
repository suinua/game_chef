<?php


namespace game_chef\models\map_data;


use game_chef\models\GameType;
use pocketmine\math\Vector3;

class FFAGameMapData extends MapData
{
    /**
     * @var Vector3[]
     */
    private array $spawnPoints;

    public function __construct(string $name, string $levelName, array $adaptedGameTypes, array $customMapVectorDataList, array $customMapArrayVectorDataList, array $spawnPoints) {
        parent::__construct($name, $levelName, $adaptedGameTypes, $customMapVectorDataList, $customMapArrayVectorDataList);
        $this->spawnPoints = $spawnPoints;
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
        $spawnPoints = [];
        foreach ($this->spawnPoints as $spawnPoint) {
            $spawnPoints[] = [
                "x" => $spawnPoint->getX(),
                "y" => $spawnPoint->getY(),
                "z" => $spawnPoint->getZ(),
            ];
        }

        return [
            "name" => $this->name,
            "level_name" => $this->levelName,
            "adapted_game_types" => array_map(fn(GameType $type) => strval($type), $this->adaptedGameTypes),
            "custom_map_vector_data_list" => $customMapVectorDataList,
            "custom_map_array_vector_data_list" => $customMapArrayVectorDataList,
            "spawn_points" => $spawnPoints,
        ];
    }

    static function fromJson(array $json): FFAGameMapData {
        $customMapVectorDataList = [];
        foreach ($json["custom_map_vector_data_list"] as $customMapVectorData) {
            $customMapVectorDataList[] = CustomMapVectorData::fromJson($customMapVectorData);
        }

        $customMapArrayVectorDataList = [];
        foreach ($json["custom_map_array_vector_data_list"] as $customMapArrayVectorData) {
            $customMapArrayVectorDataList[] = CustomMapArrayVectorData::fromJson($customMapArrayVectorData);
        }
        $spawnPoints = [];
        foreach ($json["spawn_points"] as $spawnPoint) {
            $spawnPoints[] = new Vector3(
                $spawnPoint["x"],
                $spawnPoint["y"],
                $spawnPoint["z"]
            );
        }


        return new FFAGameMapData(
            $json["name"],
            $json["level_name"],
            array_map(fn(string $type) => new GameType($type), $json["adapted_game_types"]),
            $customMapVectorDataList,
            $customMapArrayVectorDataList,
            $spawnPoints
        );
    }

    /**
     * @return Vector3[]
     */
    public function getSpawnPoints(): array {
        return $this->spawnPoints;
    }

    /**
     * @param Vector3 $vector3
     * @throws \Exception
     */
    public function addSpawnPoint(Vector3 $vector3) {
        foreach ($this->spawnPoints as $spawnPoint) {
            if ($spawnPoint->equals($vector3)) {
                throw new \Exception("同じ座標に２つ以上スポーン地点を追加することはできません");
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
        foreach ($this->spawnPoints as $key => $spawnPoint) {
            if ($spawnPoint->equals($vector3)) {
                $isExist = true;
            } else {
                $newSpawnPoints[] = $spawnPoint;
            }
        }
        if (!$isExist) {
            throw new \Exception("存在しないスポーン地点を削除することはできません");
        }

        $this->spawnPoints = $newSpawnPoints;
    }
}