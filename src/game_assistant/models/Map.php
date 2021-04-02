<?php


namespace game_assistant\models;


class Map
{
    protected string $name;
    protected string $levelName;

    public function __construct(string $name, string $levelName) {
        $this->name = $name;
        $this->levelName = $levelName;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLevelName(): string {
        return $this->levelName;
    }
}