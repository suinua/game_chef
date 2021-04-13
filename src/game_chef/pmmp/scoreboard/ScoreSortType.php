<?php


namespace game_chef\pmmp\scoreboard;


class ScoreSortType
{
    private int $value;

    public function __construct(int $value) {
        $this->value = $value;
    }

    static function smallToLarge() : ScoreSortType {
        return new ScoreSortType(0);
    }

    static function largeToSmall() : ScoreSortType {
        return new ScoreSortType(1);
    }

    public function getValue(): int {
        return $this->value;
    }
}