<?php


namespace game_assistant\models;


//Memo:マップと同期してなくてはだめ。マップにセットされてるものから選択できるように
class Team
{
    protected TeamId $id;
    protected string $name;
    protected Score $score;
    protected string $TeamColorFormat;
    protected ?int $maxPlayer;
    protected ?int $minPlayer;

    public function __construct(string $name, string $TeamColorFormat = "", ?int $maxPlayer = null, ?int $minPlayer = null) {
        $this->id = TeamId::asNew();
        $this->name = $name;
        $this->score = new Score();
        $this->TeamColorFormat = $TeamColorFormat;
        $this->maxPlayer = $maxPlayer;
        $this->minPlayer = $minPlayer;
    }

    public function getId(): TeamId {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getScore(): Score {
        return $this->score;
    }

    public function getTeamColorFormat(): string {
        return $this->TeamColorFormat;
    }

    public function getMaxPlayer(): ?int {
        return $this->maxPlayer;
    }

    public function getMinPlayer(): ?int {
        return $this->minPlayer;
    }
}



