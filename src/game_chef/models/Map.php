<?php


namespace game_chef\models;


use game_chef\models\map_data\CustomMapArrayVectorData;
use game_chef\models\map_data\CustomMapVectorData;
use pocketmine\math\Vector3;

class Map
{
    protected string $name;
    protected string $levelName;

    /**
     * @var CustomMapVectorData[]
     * CustomMapVectorData::$key => CustomMapVectorData
     */
    private array $customMapVectorDataList;

    /**
     * @var CustomMapArrayVectorData[]
     * CustomMapArrayVectorData::$key => CustomMapArrayVectorData
     */
    private array $customMapArrayVectorDataList;

    /**
     * Map constructor.
     * @param string $name
     * @param string $levelName
     * @param CustomMapVectorData[] $customMapVectorDataList
     * @param CustomMapArrayVectorData[] $customMapArrayVectorDataList
     */
    public function __construct(string $name, string $levelName, array $customMapVectorDataList, array $customMapArrayVectorDataList) {
        $this->name = $name;
        $this->levelName = $levelName;

        $this->customMapVectorDataList = [];
        foreach ($customMapVectorDataList as $data) {
            $this->customMapVectorDataList[$data->getKey()] = $data;
        }

        $this->customMapArrayVectorDataList = [];
        foreach ($customMapArrayVectorDataList as $data) {
            $this->customMapArrayVectorDataList[$data->getKey()] = $data;
        }
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLevelName(): string {
        return $this->levelName;
    }

    /**
     * @return CustomMapVectorData[]
     */
    public function getCustomMapVectorDataList(): array {
        return $this->customMapVectorDataList;
    }

    public function getCustomVectorData(string $key): ?Vector3 {
        if (array_key_exists($key, $this->customMapVectorDataList)) {
            return $this->customMapVectorDataList[$key]->getVector3();
        } else {
            return null;
        }
    }

    /**
     * @return CustomMapArrayVectorData[]
     */
    public function getCustomMapArrayVectorDataList(): array {
        return $this->customMapArrayVectorDataList;
    }

    public function getCustomArrayVectorData(string $key): array {
        if (array_key_exists($key, $this->customMapArrayVectorDataList)) {
            return $this->customMapArrayVectorDataList[$key]->getVector3List();
        } else {
            return [];
        }
    }
}