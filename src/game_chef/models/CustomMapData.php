<?php


namespace game_chef\models;


class CustomMapData
{
    private string $key;

    public function __construct(string $key) {
        $this->key = $key;
    }

    public function getKey(): string {
        return $this->key;
    }
}