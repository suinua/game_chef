<?php


namespace game_assistant\models;


class Game
{
    protected GameId $id;
    protected GameType $type;
    protected ?Score $victoryScore;
    protected GameStatus $status;
    protected bool $canJumpIn;
    protected ?int $timeLimit;

    public function __construct(GameType $gameType, ?Score $victoryScore, bool $canJumpIn = true, ?int $timeLimit = null) {
        $this->id = GameId::asNew();
        $this->type = $gameType;
        $this->victoryScore = $victoryScore;
        $this->status = GameStatus::Standby();
        $this->canJumpIn = $canJumpIn;
        $this->timeLimit = $timeLimit;
    }

    public function getId(): GameId {
        return $this->id;
    }

    public function getType(): GameType {
        return $this->type;
    }

    public function getTimeLimit(): ?int {
        return $this->timeLimit;
    }

    /**
     * @throws \Exception
     */
    public function start(): void {
        if (!$this->status->equals(GameStatus::Standby())) {
            throw new \Exception("待機状態の試合しか開始できません");
        }

        $this->status = GameStatus::Started();
    }

    /**
     * @throws \Exception
     */
    public function finished(): void {
        if ($this->status->equals(GameStatus::Started())) {
            throw new \Exception("始まっている試合しか終了できません");
        }

        $this->status = GameStatus::Finished();
    }
}