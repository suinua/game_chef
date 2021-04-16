<?php


namespace game_chef\models;


use pocketmine\math\Vector3;

class TeamDataOnMap
{
    private string $teamName;
    private string $teamColorFormat;
    protected ?int $maxPlayer;
    protected ?int $minPlayer;
    /**
     * @var Vector3[]
     */
    private array $spawnPoints;

    /**
     * @var CustomTeamVectorData[]
     * string => CustomTeamVectorData
     */
    private array $customTeamVectorDataList;
    /**
     * @var CustomTeamArrayVectorData[]
     * string => CustomTeamVectorsData
     */
    private array $customTeamArrayVectorDataList;

    /**
     * TeamDataOnMap constructor.
     * @param string $teamName
     * @param string $teamColorFormat
     * @param int|null $maxPlayer
     * @param int|null $minPlayer
     * @param Vector3[] $spawnPoints
     * @param CustomTeamVectorData[] $customTeamVectorDataList
     * @param array $customTeamArrayVectorDataList
     * @throws \Exception
     */
    public function __construct(
        string $teamName,
        string $teamColorFormat,
        ?int $maxPlayer,
        ?int $minPlayer,
        array $spawnPoints,
        array $customTeamVectorDataList,
        array $customTeamArrayVectorDataList) {
        if ($maxPlayer !== null and $minPlayer !== null) {
            if ($maxPlayer <= $minPlayer) {
                throw new \Exception("最大人数は最少人数より小さくすることはできません");
            }
        }

        if ($teamName === "") throw new \Exception("チーム名を空白にすることはできません");

        $this->teamName = $teamName;
        $this->teamColorFormat = $teamColorFormat;
        $this->spawnPoints = $spawnPoints;
        $this->maxPlayer = $maxPlayer;
        $this->minPlayer = $minPlayer;
        $this->customTeamVectorDataList = [];
        $this->customTeamArrayVectorDataList = [];

        foreach ($customTeamVectorDataList as $customTeamVectorData) {
            if ($customTeamVectorData->getTeamName() !== $teamName) {
                throw new \Exception("{$teamName}以外のカスタムチームデータを入れることはできません");
            }
            $this->customTeamVectorDataList[$customTeamVectorData->getKey()] = $customTeamVectorData;
        }

        foreach ($customTeamArrayVectorDataList as $customTeamArrayVectorData) {
            if ($customTeamArrayVectorData->getTeamName() !== $teamName) {
                throw new \Exception("{$teamName}以外のカスタムチームデータを入れることはできません");
            }
            $this->customTeamArrayVectorDataList[$customTeamArrayVectorData->getKey()] = $customTeamArrayVectorData;
        }
    }

    public function getTeamName(): string {
        return $this->teamName;
    }

    public function getTeamColorFormat(): string {
        return $this->teamColorFormat;
    }

    public function getMinPlayer(): ?int {
        return $this->minPlayer;
    }

    public function getMaxPlayer(): ?int {
        return $this->maxPlayer;
    }

    /**
     * @return Vector3[]
     */
    public function getSpawnPoints(): array {
        return $this->spawnPoints;
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
}