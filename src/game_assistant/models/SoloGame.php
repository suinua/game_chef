<?php


namespace game_assistant\models;


class SoloGame extends Game
{
    private SoloGameMap $map;
    /**
     * @var SoloTeam[]
     * name => SoloTeam
     */
    private array $teams;
    protected ?int $maxPlayers;

    public function __construct(SoloGameMap $map, GameType $gameType, Score $victoryScore, bool $canJumpIn = true, ?int $timeLimit = null, array $teams = [], ?int $maxPlayers = null) {
        parent::__construct($gameType, $victoryScore, $canJumpIn, $timeLimit);
        $this->map = $map;
        $this->teams = $teams;
        $this->maxPlayers = $maxPlayers;
    }

    public function canJoin(string $playerName): bool {
        if (array_key_exists($playerName, $this->teams)) return false;
        if ($this->maxPlayers !== null) {
            if (count($this->teams) >= $this->maxPlayers) return false;
        }
        if ($this->status->equals(GameStatus::Finished())) return false;
        if ($this->status->equals(GameStatus::Standby())) return true;
        if ($this->status->equals(GameStatus::Started())) return $this->canJumpIn;
        return false;
    }

    public function getTeams(): array {
        return $this->teams;
    }

    public function getMap(): SoloGameMap {
        return $this->map;
    }

    public function getMaxPlayers(): ?int {
        return $this->maxPlayers;
    }

    /**
     * @param SoloTeam $team
     * @throws \Exception
     */
    public function addSoloTeam(SoloTeam $team): void {
        if ($this->canJoin($team->getName())) {
            throw new \Exception("ソロチームを追加できませんでした");
        }

        $this->teams[$team->getName()] = $team;
    }


    /**
     * @param string $name
     * @param Score $score
     * @throws \Exception
     */
    public function addScore(string $name, Score $score): void {
        if (!array_key_exists($name, $this->teams)) {
            throw new \Exception("{$name}はこのゲームに参加していません");
        }
        $this->teams[$name]->addScore($score);
    }
}