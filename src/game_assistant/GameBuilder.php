<?php


namespace game_assistant;


use game_assistant\models\Score;

abstract class GameBuilder
{
    protected ?int $timeLimit = null;//共通
    protected ?Score $victoryScore = null;//共通
    protected bool $canJumpIn = false;//共通
    protected ?int $maxPlayersDifference = null;//共通

    public function setTimeLimit(?int $timeLimit): void {
        $this->timeLimit = $timeLimit;
    }

    public function setVictoryScore(?Score $victoryScore): void {
        $this->victoryScore = $victoryScore;
    }

    public function setCanJumpIn(bool $canJumpIn): void {
        $this->canJumpIn = $canJumpIn;
    }

    public function setMaxPlayersDifference(?int $difference): void {
        $this->maxPlayersDifference = $difference;
    }
}