<?php


namespace game_assistant\pmmp\events;


use game_assistant\models\GameId;
use pocketmine\event\Event;

class UpdatedGameTimerEvent extends Event
{
    private GameId $gameId;
    private ?int $timeLimit;
    private int $elapsedTime;

    public function __construct(GameId $gameId, ?int $timeLimit, int $elapsedTime) {
        $this->gameId = $gameId;
        $this->timeLimit = $timeLimit;
        $this->elapsedTime = $elapsedTime;
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }

    public function getTimeLimit(): ?int {
        return $this->timeLimit;
    }

    public function getElapsedTime(): int {
        return $this->elapsedTime;
    }
}