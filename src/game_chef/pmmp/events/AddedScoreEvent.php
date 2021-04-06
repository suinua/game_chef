<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\Score;
use game_chef\models\TeamId;
use pocketmine\event\Event;

class AddedScoreEvent extends Event
{
    private GameId $gameId;
    private TeamId $teamId;
    private Score $totalScore;
    private Score $scoreAdded;

    public function __construct(GameId $gameId, TeamId $teamId, Score $totalScore, Score $scoreAdded) {
        $this->gameId = $gameId;
        $this->teamId = $teamId;
        $this->totalScore = $totalScore;
        $this->scoreAdded = $scoreAdded;
    }

    public function getGameId(): GameId {
        return $this->gameId;
    }

    public function getTeamId(): TeamId {
        return $this->teamId;
    }

    public function getTotalScore(): Score {
        return $this->totalScore;
    }

    public function getScoreAdded(): Score {
        return $this->scoreAdded;
    }
}