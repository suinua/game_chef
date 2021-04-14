<?php


namespace game_chef\models;


use pocketmine\math\Vector3;

class FFAGameMap extends Map
{
    /**
     * @var Vector3[]
     */
    private array $spawnPoints;

    public function __construct(string $name, string $levelName, array $adaptedGameType, array $customMapVectorDataList, array $customMapVectorsDataList, array $spawnPoints) {
        parent::__construct($name, $levelName, $adaptedGameType, $customMapVectorDataList, $customMapVectorsDataList);
        $this->spawnPoints = $spawnPoints;
    }

    /**
     * @return Vector3[]
     */
    public function getSpawnPoints(): array {
        return $this->spawnPoints;
    }
}