<?php


namespace game_assistant\models;


class Score
{
    private int $value;

    public function __construct(int $value) {
        $this->value = $value;
    }

    static function asNew(): self {
        return new Score(0);
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }
}