<?php


namespace game_chef\models\map_data;


class CustomTeamData
{
    protected string $key;
    protected string $teamName;

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