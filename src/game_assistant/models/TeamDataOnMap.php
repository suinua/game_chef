<?php


namespace game_assistant\models;


class TeamDataOnMap
{
    private string $teamName;
    private string $teamColorFormat;
    protected ?int $maxPlayer;
    protected ?int $minPlayer;
    private array $spawnPoints;

    /**
     * TeamDataOnMap constructor.
     * @param string $teamName
     * @param string $teamColorFormat
     * @param int|null $maxPlayer
     * @param int|null $minPlayer
     * @param array $spawnPoints
     * @throws \Exception
     */
    public function __construct(string $teamName, string $teamColorFormat, ?int $maxPlayer, ?int $minPlayer, array $spawnPoints) {
        if ($this->maxPlayer !== null and $this->minPlayer !== null) {
            if ($this->maxPlayer <= $this->minPlayer) {
                throw new \Exception("最大人数は最少人数より小さくすることはできません");
            }
        }

        $this->teamName = $teamName;
        $this->teamColorFormat = $teamColorFormat;
        $this->spawnPoints = $spawnPoints;
        $this->maxPlayer = $maxPlayer;
        $this->minPlayer = $minPlayer;
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

    public function getSpawnPoints(): array {
        return $this->spawnPoints;
    }
}