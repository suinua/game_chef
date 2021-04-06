<?php


namespace game_chef\models;


class GameType
{
    private string $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    public function __toString() {
        return $this->text;
    }

    public function equals(?self $type): bool {
        if ($type === null)
            return false;

        return $this->text === $type->text;
    }
}