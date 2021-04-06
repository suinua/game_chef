<?php


namespace game_chef\models;


use game_chef\pmmp\events\UpdatedGameTimerEvent;
use game_chef\services\GameService;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;

class GameTimer
{
    private GameId $gameId;

    private ?int $timeLimit;
    private int $elapsedTime;

    private TaskHandler $handler;

    public function __construct(GameId $gameId, ?int $timeLimit) {
        $this->gameId = $gameId;
        $this->timeLimit = $timeLimit;
        $this->elapsedTime = 0;
    }

    public function start(TaskScheduler $scheduler): void {
        $this->handler = $scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick): void {
            $this->elapsedTime++;
            (new UpdatedGameTimerEvent($this->gameId, $this->timeLimit, $this->elapsedTime))->call();

            if ($this->timeLimit !== null) {
                if ($this->elapsedTime >= $this->timeLimit) {
                    GameService::finish($this->gameId);
                }
            }
        }), 20);
    }

    /**
     * @throws \Exception
     */
    public function stop(): void {
        if ($this->handler === null) {
            throw new \Exception("スタートしていないタイマーを止めることはできません");
        } else if ($this->handler->isCancelled()) {
            throw new \Exception("すでにキャンセルされているタイマーを止めることは出来ません");
        } else {
            $this->handler->cancel();
        }
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }
}