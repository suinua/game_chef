<?php


namespace game_chef\pmmp\events;


use game_chef\models\GameId;
use game_chef\models\GameType;
use game_chef\models\Score;
use game_chef\models\TeamId;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;

class AddScoreEvent extends Event implements Cancellable
{
    private GameId $gameId;
    private GameType $gameType;
    private TeamId $teamId;
    private Score $totalScore;
    private Score $scoreAdded;

    public function __construct(GameId $gameId, GameType $gameType, TeamId $teamId, Score $totalScore, Score $scoreAdded) {
        $this->gameId = $gameId;
        $this->gameType = $gameType;
        $this->teamId = $teamId;
        $this->totalScore = $totalScore;
        $this->scoreAdded = $scoreAdded;
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

    public function getTotalScore(): Score {
        return $this->totalScore;
    }

    public function getScoreAdded(): Score {
        return $this->scoreAdded;
    }
}