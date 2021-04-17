<?php


namespace game_chef\models\map_data;


use game_chef\models\GameType;

class MapData
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
    protected array $customMapVectorDataList;
    /**
     * @var CustomMapArrayVectorData[]
     */
    protected array $customMapArrayVectorDataList;

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

    /**
     * @return CustomMapVectorData[]
     */
    public function getCustomMapVectorDataList(): array {
        return $this->customMapVectorDataList;
    }

    /**
     * @return CustomMapArrayVectorData[]
     */
    public function getCustomMapArrayVectorDataList(): array {
        return $this->customMapArrayVectorDataList;
    }

    /**
     * @param GameType[] $adaptedGameTypes
     */
    public function setAdaptedGameTypes(array $adaptedGameTypes): void {
        $this->adaptedGameTypes = $adaptedGameTypes;
    }

    public function isAdaptedGameType(GameType $gameType): bool {
        foreach ($this->adaptedGameTypes as $adaptedGameType) {
            if ($gameType->equals($adaptedGameType)) {
                return true;
            }
        }

        return false;
    }
}