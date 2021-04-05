<?php


namespace game_assistant;


use game_assistant\models\GameType;
use game_assistant\models\Score;

abstract class GameBuilder
{
    protected GameType $gameType;
    protected ?int $timeLimit = null;
    protected ?Score $victoryScore = null;
    protected bool $canJumpIn = false;

    /**
     * @param GameType $gameType
     * @throws \Exception
     */
    public function setGameType(GameType $gameType): void {
        if ($this->gameType !== null) throw new \Exception("再度セットすることは出来ません");
        $this->gameType = $gameType;
    }

    public function setTimeLimit(?int $timeLimit): void {
        $this->timeLimit = $timeLimit;
    }

    public function setVictoryScore(?Score $victoryScore): void {
        $this->victoryScore = $victoryScore;
    }

    public function setCanJumpIn(bool $canJumpIn): void {
        $this->canJumpIn = $canJumpIn;
    }

}