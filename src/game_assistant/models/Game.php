<?php


namespace game_assistant\models;


class Game
{
    protected GameId $id;
    protected GameType $type;
    protected Score $victoryScore;
    protected GameStatus $status;
    protected bool $canJumpIn;


    public function getId(): GameId {
        return $this->id;
    }

    public function getType(): GameType {
        return $this->type;
    }

    public function start(): void {
        if (!$this->status->equals(GameStatus::Standby())) {
            throw new \Exception("待機状態の試合しか開始できません");
        }

        $this->status = GameStatus::Started();
    }

    public function finished(): void {
        if ($this->status->equals(GameStatus::Started())) {
            throw new \Exception("始まっている試合しか終了できません");
        }

        $this->status = GameStatus::Finished();
    }
}