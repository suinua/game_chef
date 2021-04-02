<?php


namespace game_assistant\models;


use game_assistant\events\FinishedGameEvent;
use game_assistant\events\UpdatedGameTimerEvent;
use game_assistant\services\GameService;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class GameTimer
{
    private GameId $gameId;

    private int $timeLimit;
    private int $elapsedTime;

    public function __construct(GameId $gameId, int $timeLimit) {
        $this->gameId = $gameId;
        $this->timeLimit = $timeLimit;
        $this->elapsedTime = 0;
    }

    public function start(TaskScheduler $scheduler): void {
        $scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick): void {
            $this->elapsedTime++;
            (new UpdatedGameTimerEvent($this->gameId))->call();

            if ($this->elapsedTime >= $this->timeLimit) {
                GameService::finish($this->gameId);
            }
        }), 20);
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }
}