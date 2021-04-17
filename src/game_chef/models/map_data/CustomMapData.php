<?php


namespace game_chef\models\map_data;


class CustomMapData
{
    protected string $key;

    public function __construct(string $key) {
        $this->key = $key;
    }

    public function getKey(): string {
        return $this->key;
    }
}