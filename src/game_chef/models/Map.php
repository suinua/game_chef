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
     * @var CustomMapVectorsData[]
     */
    private array $customMapVectorsDataList;

    public function __construct(string $name, string $levelName, array $adaptedGameTypes) {
        $this->name = $name;
        $this->levelName = $levelName;
        $this->adaptedGameTypes = $adaptedGameTypes;
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
     * @return CustomMapVectorsData[]
     */
    public function getCustomMapVectorsDataList(): array {
        return $this->customMapVectorsDataList;
    }

    public function getCustomVectorsData(string $key): array {
        if (array_key_exists($key, $this->customMapVectorsDataList)) {
            return $this->customMapVectorsDataList[$key]->getVector3List();
        } else {
            return [];
        }
    }
}