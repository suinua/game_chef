<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use game_chef\models\Score;
use game_chef\models\TeamId;
use pocketmine\event\Event;

class AddedScoreEvent extends Event
{
    private GameId $gameId;
    private GameType $gameType;
    private TeamId $teamId;
    private Score $currentScore;
    private Score $addedScore;

    public function __construct(GameId $gameId, GameType $gameType, TeamId $teamId, Score $totalScore, Score $score) {
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->teamId = $teamId;
        $this->currentScore = $totalScore;
        $this->addedScore = $score;
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }

    public function getGameType(): GameType {
        return $this->gameType;
    }

    public function getTeamId(): TeamId {
        return $this->teamId;
    }

    public function getCurrentScore(): Score {
        return $this->currentScore;
    }

    public function getAddedScore(): Score {
        return $this->addedScore;
    }

    public function setAddedScore(Score $addedScore): void {
        $this->addedScore = $addedScore;
    }
}