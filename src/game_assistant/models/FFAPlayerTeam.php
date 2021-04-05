<?php


namespace game_assistant\models;


class FFAPlayerTeam
{
    private TeamId $id;
    private string $name;
    private Score $score;
    private string $TeamColorFormat;

    public function __construct(string $name, string $TeamColorFormat = "") {
        $this->id = TeamId::asNew();
        $this->name = $name;
        $this->score = new Score();
        $this->TeamColorFormat = $TeamColorFormat;
    }

    public function addScore(Score $score): void {
        $this->score = $this->score->add($score);
    }

    /**
     * @return TeamId
     */
    public function getId(): TeamId {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return Score
     */
    public function getScore(): Score {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getTeamColorFormat(): string {
        return $this->TeamColorFormat;
    }
}