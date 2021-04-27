<?php


namespace game_chef\models;


use game_chef\pmmp\events\AddScoreEvent;
use game_chef\services\GameService;
use game_chef\store\PlayerDataStore;

class TeamGame extends Game
{

    private TeamGameMap $map;
    /**
     * @var Team[]
     */
    private array $teams;
    private bool $friendlyFire;
    protected ?int $maxPlayersDifference;
    private bool $canMoveTeam;


    public function __construct(TeamGameMap $map, GameType $gameType, ?Score $victoryScore, bool $canJumpIn = true, ?int $timeLimit = null, array $teams = [], bool $friendlyFire = false, ?int $maxPlayersDifference = null, bool $canMoveTeam = true) {
        parent::__construct($gameType, $victoryScore, $canJumpIn, $timeLimit);
        $this->map = $map;
        $this->teams = $teams;
        $this->friendlyFire = $friendlyFire;
        $this->maxPlayersDifference = $maxPlayersDifference;
        $this->canMoveTeam = $canMoveTeam;
    }

    /**
     * @param string $playerName
     * @return bool
     */
    public function canJoin(string $playerName): bool {
        try {
            $playerData = PlayerDataStore::getByName($playerName);
        } catch (\Exception $e) {
            return false;
        }
        if ($playerData->getBelongGameId() !== null) return false;

        $hasEmpty = false;
        foreach ($this->teams as $team) {
            if ($team->getMaxPlayer() === null) {
                $hasEmpty = true;
                break;
            }

            $teamPlayerDataList = PlayerDataStore::getByTeamId($team->getId());
            if (($team->getMaxPlayer() - count($teamPlayerDataList)) >= 1) {
                $hasEmpty = true;
                break;
            }
        }

        if (!$hasEmpty) return false;
        if ($this->status->equals(GameStatus::Finished())) return false;
        if ($this->status->equals(GameStatus::Standby())) return true;
        if ($this->status->equals(GameStatus::Started())) return $this->canJumpIn;
        return false;
    }

    /**
     * @return Team[]
     */
    public function getTeams(): array {
        return $this->teams;
    }

    /**
     * @param TeamId $teamId
     * @return Team
     * @throws \Exception
     */
    public function getTeamById(TeamId $teamId): Team {
        foreach ($this->teams as $team) {
            if ($team->getId()->equals($teamId)) return $team;
        }

        throw new \Exception("そのIDのチームは存在しません");
    }

    public function findTeamById(TeamId $teamId): ?Team {
        foreach ($this->teams as $team) {
            if ($team->getId()->equals($teamId)) return $team;
        }

        return null;
    }

    public function getMaxPlayersDifference(): ?int {
        return $this->maxPlayersDifference;
    }

    public function isCanMoveTeam(): bool {
        return $this->canMoveTeam;
    }

    /**
     * @param TeamId $teamId
     * @param Score $score
     * @throws \Exception
     */
    public function addScore(TeamId $teamId, Score $score): void {
        $this->getTeamById($teamId)->addScore($score);
        $team = $this->getTeamById($teamId);

        (new AddScoreEvent($this->id, $this->type, $teamId, $team->getScore(), $score))->call();
        if ($this->victoryScore === null) return;
        if ($team->getScore()->isBiggerThan($this->victoryScore)) {
            GameService::finish($this->id);
        }
    }

    public function getFriendlyFire(): bool {
        return $this->friendlyFire;
    }

    /**
     * @return TeamGameMap
     */
    public function getMap(): TeamGameMap {
        return $this->map;
    }

    public function getMaxPlayers(): ?int {
        $count = 0;
        $unlimited = true;
        foreach ($this->teams as $team) {
            if ($team->getMaxPlayer() !== null) {
                $unlimited = false;
                $count += $team->getMaxPlayer();
            }
        }

        return $unlimited ? null : $count;
    }
}