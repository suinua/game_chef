<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use pocketmine\event\Event;

class UpdatedGameTimerEvent extends Event
{
    private GameId $gameId;
    private GameType $gameType;
    private ?int $timeLimit;
    private int $elapsedTime;

    public function __construct(GameId $gameId, GameType $gameType, ?int $timeLimit, int $elapsedTime) {
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->timeLimit = $timeLimit;
        $this->elapsedTime = $elapsedTime;
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }

    public function getGameType(): GameType {
        return $this->gameType;
    }


    public function getTimeLimit(): ?int {
        return $this->timeLimit;
    }

    public function getElapsedTime(): int {
        return $this->elapsedTime;
    }
}