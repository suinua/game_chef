<?php


namespace game_assistant\models;


class Game
{
    protected GameId $id;
    protected GameType $type;
    protected Score $victoryScore;
    protected GameStatus $status;

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

class GameId
{
    private string $value;

    public function __construct(string $value) {
        $this->value = $value;
    }

    static function asNew(): self {
        return new GameId(uniqid());
    }

    public function __toString() {
        return $this->value;
    }

    public function equals(?self $id): bool {
        if ($id === null)
            return false;

        return $this->value === $id->value;
    }
}

class GameType
{
    /**
     * @var string
     */
    private $text;

    public function __construct(string $text) {
        $this->text = $text;
    }

    public function __toString() {
        return $this->text;
    }

    public function equals(?self $type): bool {
        if ($type === null)
            return false;

        return $this->text === $type->text;
    }
}

class GameStatus
{
    private string $value;

    private function __construct(string $value) {
        $this->value = $value;
    }

    static function Standby(): self {
        return new self("Standby");
    }

    static function Started(): self {
        return new self("Started");
    }

    static function Finished(): self {
        return new self("Finished");
    }

    public function equals(?self $status): bool {
        if ($status === null)
            return false;

        return $this->value === $status->value;
    }
}

