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

    /**
     * @param string $key
     * @return CustomMapArrayVectorData
     * @throws \Exception
     */
    public function getCustomMapArrayVectorData(string $key): CustomMapArrayVectorData {
        foreach ($this->customMapArrayVectorDataList as $customMapArrayVectorData) {
            if ($customMapArrayVectorData->getKey() === $key) {
                return $customMapArrayVectorData;
            }
        }

        throw new \Exception("そのkey({$key})の配列型カスタム座標データは存在しません");
    }

    /**
     * @param CustomMapArrayVectorData $target
     * @throws \Exception
     */
    public function addCustomMapArrayVectorData(CustomMapArrayVectorData $target) {
        foreach ($this->customMapVectorDataList as $data) {
            if ($data->getKey() === $target->getKey()) {
                throw new \Exception("同じKeyの配列型座標データを追加することはできません");
            }
        }
        $this->customMapArrayVectorDataList[] = $target;
    }

    /**
     * @param CustomMapArrayVectorData $target
     * @throws \Exception
     */
    public function updateCustomMapArrayVectorData(CustomMapArrayVectorData $target) {
        $isExist = false;
        foreach ($this->customMapArrayVectorDataList as $index => $customMapArrayVectorData) {
            if ($customMapArrayVectorData->getKey() === $target->getKey()) {
                $isExist = true;
                $this->customMapArrayVectorDataList[$index] = $target;
            }
        }

        if (!$isExist) {
            throw new \Exception("存在しない配列型カスタム座標データを更新することはできません");
        }
    }

    /**
     * @param string $key
     * @return CustomMapVectorData
     * @throws \Exception
     */
    public function getCustomMapVectorData(string $key): CustomMapVectorData {
        foreach ($this->customMapVectorDataList as $customMapVectorData) {
            if ($customMapVectorData->getKey() === $key) {
                return $customMapVectorData;
            }
        }

        throw new \Exception("そのkey({$key})のカスタム座標データは存在しません");
    }

    /**
     * @param CustomMapVectorData $customMapVectorData
     * @throws \Exception
     */
    public function addCustomMapVectorData(CustomMapVectorData $customMapVectorData) {
        foreach ($this->customMapVectorDataList as $data) {
            if ($data->getKey() === $customMapVectorData->getKey()) {
                throw new \Exception("同じKeyの座標データを追加することはできません");
            }
        }
        $this->customMapVectorDataList[] = $customMapVectorData;
    }

    /**
     * @param CustomMapVectorData $customMapVectorData
     * @throws \Exception
     */
    public function updateCustomMapVectorData(CustomMapVectorData $customMapVectorData) {
        $isExist = false;
        foreach ($this->customMapVectorDataList as $index => $data) {
            if ($data->getKey() === $customMapVectorData->getKey()) {
                $isExist = true;
                $this->customMapVectorDataList[$index] = $customMapVectorData;
            }
        }

        if (!$isExist) {
            throw new \Exception("存在しないカスタム座標データを更新することはできません");
        }
    }

    /**
     * @param CustomMapVectorData $target
     * @throws \Exception
     */
    public function deleteCustomMapVectorData(CustomMapVectorData $target) {
        $isExist = false;
        $newList = [];
        foreach ($this->customMapVectorDataList as $data) {
            if ($data->getKey() === $target->getKey()) {
                $isExist = true;
            } else {
                $newList[] = $data;
            }
        }

        if (!$isExist) {
            throw new \Exception("存在しないカスタム座標データを削除することはできません");
        }

        $this->customMapVectorDataList = array_values($newList);
    }
}