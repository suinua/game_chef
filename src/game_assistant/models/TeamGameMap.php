<?php


namespace game_assistant\models;


use pocketmine\math\Vector3;

class TeamGameMap extends Map
{
    /**
     * @var string[]
     */
    private array $teamNames;
    /**
     * @var SpawnPointGroup[]
     */
    private array $spawnPointGroupList;

    //TODO:teamNamesとspawnPointGroupListが一致しなきゃダメ
    public function __construct(string $name, string $levelName, array $teamNames, array $spawnPointGroupList) {
        parent::__construct($name, $levelName);
        $this->teamNames = $teamNames;
        $this->spawnPointGroupList = $spawnPointGroupList;
    }
}

class SpawnPointGroup
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
