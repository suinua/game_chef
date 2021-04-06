<?php


namespace game_chef\models;


class Score
{
    private int $value;

    public function __construct(int $value = 0) {
        $this->value = $value;
    }

    public function getValue(): int {
        return $this->value;
    }

    public function add(Score $score): Score {
        return new Score($this->value + $score->value);
    }
}