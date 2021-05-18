<?php


namespace game_chef\pmmp\bossbar;


use game_chef\models\GameType;

class BossbarType
{
    private string $value;

    public function __construct(string $value) {
        $this->value = $value;
    }

    public function equals(?BossbarType $id): bool {
        if ($id === null)
            return false;

        return $this->value === $id->value;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->value;
    }

    public function toGameType(): GameType {
        return new GameType($this->value);
    }
}