<?php


namespace game_assistant\models;


use pocketmine\math\Vector3;

class TeamSpawnPointBunch
{
    private string $teamName;
    /**
     * @var Vector3[]
     */
    private array $spawnPoints;

    public function __construct(string $teamName, array $spawnPoints) {
        $this->teamName = $teamName;
        $this->spawnPoints = $spawnPoints;
    }

    public function getTeamName(): string {
        return $this->teamName;
    }

    public function getSpawnPoints(): array {
        return $this->spawnPoints;
    }
}