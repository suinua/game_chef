<?php


namespace game_chef\models;


use pocketmine\math\Vector3;

class CustomTeamData
{
    private string $key;
    private string $teamName;

    public function __construct(string $key, string $teamName) {
        $this->key = $key;
        $this->teamName = $teamName;
    }

    public function getKey(): string {
        return $this->key;
    }

    public function getTeamName(): string {
        return $this->teamName;
    }
}