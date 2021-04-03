<?php


namespace game_assistant\models;


class SoloGame extends Game
{
    /**
     * @var SoloTeam[]
     * name => SoloTeam
     */
    private array $teams;
    private SoloGameMap $map;

    protected int $maxPlayers;

    public function canJoin(string $playerName): bool {
        if (array_key_exists($playerName, $this->teams)) return false;
        if (count($this->teams) >= $this->maxPlayers) return false;
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

    public function getMaxPlayers(): int {
        return $this->maxPlayers;
    }

    public function addSoloTeam(SoloTeam $team): void {
        if ($this->canJoin($team->getName())) {
            throw new \Exception("ソロチームを追加できませんでした");
        }

        $this->teams[$team->getName()] = $team;
    }
}