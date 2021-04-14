<?php


namespace game_chef\models;


//TODO:extends TeamDataOnMapで実装する
use pocketmine\math\Vector3;

class Team
{
    private TeamId $id;
    private string $name;
    private Score $score;
    private string $TeamColorFormat;
    private ?int $maxPlayer;
    private ?int $minPlayer;
    /**
     * @var Vector3[]
     */
    private array $spawnPoints;
    /**
     * @var CustomTeamVectorData[]
     */
    private array $customTeamVectorDataList;
    /**
     * @var CustomTeamArrayVectorData[]
     */
    private array $customTeamArrayVectorDataList;

    /**
     * Team constructor.
     * @param string $name
     * @param array $spawnPoints
     * @param string $TeamColorFormat
     * @param int|null $maxPlayer
     * @param int|null $minPlayer
     * @param CustomTeamVectorData[] $customTeamVectorDataList
     * @param CustomTeamArrayVectorData[] $customTeamArrayVectorDataList
     * @throws \Exception
     */
    public function __construct(
        string $name,
        array $spawnPoints,
        string $TeamColorFormat = "",
        ?int $maxPlayer = null,
        ?int $minPlayer = null,
        array $customTeamVectorDataList = [],
        array $customTeamArrayVectorDataList = []) {
        if ($this->maxPlayer !== null and $this->minPlayer !== null) {
            if ($this->maxPlayer <= $this->minPlayer) {
                throw new \Exception("最大人数は最少人数より小さくすることはできません");
            }
        }

        $this->id = TeamId::asNew();
        $this->name = $name;
        $this->score = new Score();
        $this->TeamColorFormat = $TeamColorFormat;
        $this->maxPlayer = $maxPlayer;
        $this->minPlayer = $minPlayer;
        $this->spawnPoints = $spawnPoints;
        $this->customTeamVectorDataList = $customTeamVectorDataList;
        $this->customTeamArrayVectorDataList = $customTeamArrayVectorDataList;
    }

    public function getId(): TeamId {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getScore(): Score {
        return $this->score;
    }

    public function getTeamColorFormat(): string {
        return $this->TeamColorFormat;
    }

    public function getMaxPlayer(): ?int {
        return $this->maxPlayer;
    }

    public function getMinPlayer(): ?int {
        return $this->minPlayer;
    }

    public function addScore(Score $score): void {
        $this->score = $this->score->add($score);
    }

    /**
     * @return Vector3[]
     */
    public function getSpawnPoints(): array {
        return $this->spawnPoints;
    }

    public function getCustomVectorData(string $key): ?Vector3 {
        if (array_key_exists($key, $this->customTeamVectorDataList)) {
            return $this->customTeamVectorDataList[$key]->getVector3();
        } else {
            return null;
        }
    }

    public function getCustomArrayVectorData(string $key): array {
        if (array_key_exists($key, $this->customTeamVectorDataList)) {
            return $this->customTeamArrayVectorDataList[$key]->getVector3List();
        } else {
            return [];
        }
    }
}



