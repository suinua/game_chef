<?php


namespace game_chef\models;


use pocketmine\math\Vector3;

class Map
{
    protected string $name;
    protected string $levelName;
    /**
     * @var GameType[]
     */
    protected array $adaptedGameTypes;

    /**
     * @var CustomMapVectorData[]
     */
    private array $customMapVectorDataList;

    /**
     * @var CustomMapArrayVectorData[]
     */
    private array $customMapArrayVectorDataList;

    public function __construct(string $name, string $levelName, array $adaptedGameTypes, array $customMapVectorDataList, array $customMapArrayVectorDataList) {
        $this->name = $name;
        $this->levelName = $levelName;
        $this->adaptedGameTypes = $adaptedGameTypes;
        $this->customMapVectorDataList = $customMapVectorDataList;
        $this->customMapArrayVectorDataList = $customMapArrayVectorDataList;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLevelName(): string {
        return $this->levelName;
    }

    /**
     * @return GameType[]
     */
    public function getAdaptedGameTypes(): array {
        return $this->adaptedGameTypes;
    }

    public function isAdaptedGameType(GameType $gameType): bool {
        foreach ($this->adaptedGameTypes as $adaptedGameType) {
            if ($gameType->equals($adaptedGameType)) {
                return true;
            }
        }

        return false;
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