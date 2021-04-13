<?php


namespace game_chef\pmmp\bossbar;


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
}