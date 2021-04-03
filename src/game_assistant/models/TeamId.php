<?php


namespace game_assistant\models;


class TeamId
{
    private string $value;

    public function __construct(string $value) {
        $this->value = $value;
    }

    static function asNew(): self {
        return new TeamId(uniqid());
    }

    public function __toString() {
        return $this->value;
    }

    public function equals(?self $id): bool {
        if ($id === null)
            return false;

        return $this->value === $id->value;
    }
}