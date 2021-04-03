<?php


namespace game_assistant\models;


class Score
{
    private int $value;

    public function __construct(int $value = 0) {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }
}