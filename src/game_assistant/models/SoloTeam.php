<?php


namespace game_assistant\models;


class SoloTeam extends Team
{
    protected ?int $maxPlayer = 1;
    protected ?int $minPlayer = 1;

    public function __construct(string $name, string $TeamColorFormat = "") {
        parent::__construct($name, $TeamColorFormat);
    }

    public function addScore(Score $score): void {
        $this->score = $this->score->add($score);
    }
}