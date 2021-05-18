<?php


namespace game_chef\models;


use game_chef\pmmp\bossbar\BossbarType;

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

    public function toBossbarType(): BossbarType {
        return new BossbarType($this->text);
    }
}