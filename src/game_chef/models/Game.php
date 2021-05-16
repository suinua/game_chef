<?php


namespace game_chef\models;


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

    public function getStatus(): GameStatus {
        return $this->status;
    }

    public function canJumpIn(): bool {
        return $this->canJumpIn();
    }

    public function getVictoryScore(): Score {
        return $this->victoryScore;
    }

    public function start(): void {
        if (!$this->status->equals(GameStatus::Standby())) {
            throw new \LogicException("待機状態(Standby)の試合しか開始できません。この試合は{$this->status}状態です");
        }

        $this->status = GameStatus::Started();
    }

    public function finished(): void {
        if (!$this->status->equals(GameStatus::Started())) {
            throw new \LogicException("開始状態(Started)の試合しか終了できません。この試合は{$this->status}状態です");
        }

        $this->status = GameStatus::Finished();
    }
}