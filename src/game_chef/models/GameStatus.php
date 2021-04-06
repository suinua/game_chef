<?php


namespace game_chef\models;


class GameStatus
{
    private string $value;

    private function __construct(string $value) {
        $this->value = $value;
    }

    static function Standby(): self {
        return new self("Standby");
    }

    static function Started(): self {
        return new self("Started");
    }

    static function Finished(): self {
        return new self("Finished");
    }

    public function equals(?self $status): bool {
        if ($status === null)
            return false;

        return $this->value === $status->value;
    }
}