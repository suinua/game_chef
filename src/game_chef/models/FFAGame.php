<?php


namespace game_chef\models;

//FFAみたいなチームの概念がなくてランダムな場所にスポーンするゲームを指す
use game_chef\pmmp\events\AddedScoreEvent;
use game_chef\pmmp\events\AddScoreEvent;
use game_chef\services\GameService;

class FFAGame extends Game
{
    private FFAGameMap $map;
    /**
     * @var FFAPlayerTeam[]
     * name => FFATeam
     */
    private array $teams;
    protected ?int $maxPlayers;

    public function __construct(FFAGameMap $map, GameType $gameType, Score $victoryScore, bool $canJumpIn = true, ?int $timeLimit = null, array $teams = [], ?int $maxPlayers = null) {
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

    /**
     * @return FFAPlayerTeam[]
     */
    public function getTeams(): array {
        return array_values($this->teams);
    }

    public function getTeamById(TeamId $teamId): Team {
        foreach ($this->teams as $team) {
            if ($team->getId()->equals($teamId)) return $team;
        }

        throw new \LogicException("存在しないチーム({$teamId})を取得することはできません");
    }

    public function getTeamByPlayerName(string $playerName): FFAPlayerTeam {
        if (array_key_exists($playerName, $this->teams)) {
            return $this->teams[$playerName];
        } else {
            throw new \LogicException("そのプレイヤー({$playerName})のチームは存在しません");
        }
    }

    public function getMap(): FFAGameMap {
        return $this->map;
    }

    public function getMaxPlayers(): ?int {
        return $this->maxPlayers;
    }

    public function addFFATeam(FFAPlayerTeam $team): bool {
        if (!$this->canJoin($team->getName())) {
            return false;
        }

        $this->teams[$team->getName()] = $team;
        return true;
    }

    public function deleteTeam(string $name) {
        if (!array_key_exists($name, $this->teams)) {
            throw new \LogicException("参加していないプレイヤーのチーム($name)を削除することはできません");
        }

        unset($this->teams[$name]);
    }


    public function addScore(string $name, Score $score): void {
        if (!array_key_exists($name, $this->teams)) {
            throw new \LogicException("{$name}はこのゲームに参加していません");
        }
        $team = $this->teams[$name];

        $event = new AddScoreEvent($this->id, $this->type, $team->getId(), $team->getScore(), $score);
        $event->call();

        if ($event->isCancelled()) return;

        $team->addScore($event->getAddScore());
        (new AddedScoreEvent($this->id, $this->type, $team->getId(), $team->getScore(), $score))->call();
        if ($this->victoryScore === null) return;
        if ($team->getScore()->isBiggerThan($this->victoryScore)) {
            GameService::finish($this->id);
        }
    }
}