<?php


namespace game_assistant\models;


use pocketmine\math\Vector3;

class SoloGameMap extends Map
{
    /**
     * @var Vector3[]
     */
    private array $spawnPoints;

    public function __construct(string $name, string $levelName, array $spawnPoints) {
        parent::__construct($name, $levelName);
        $this->spawnPoints = $spawnPoints;
    }
}