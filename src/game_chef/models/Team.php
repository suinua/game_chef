<?php


namespace game_chef\models;


//Memo:マップと同期してなくてはだめ。マップにセットされてるものから選択できるように
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
     * Team constructor.
     * @param string $name
     * @param array $spawnPoints
     * @param string $TeamColorFormat
     * @param int|null $maxPlayer
     * @param int|null $minPlayer
     * @throws \Exception
     */
    public function __construct(string $name, array $spawnPoints, string $TeamColorFormat = "", ?int $maxPlayer = null, ?int $minPlayer = null) {
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
}



