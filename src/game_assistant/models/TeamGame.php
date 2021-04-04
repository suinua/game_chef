<?php


namespace game_assistant\models;


use game_assistant\store\PlayerDataStore;

class TeamGame extends Game
{
    /**
     * @var Team[]
     */
    private array $teams;
    private TeamGameMap $map;

    protected int $maxPlayersDifference;
    private bool $canMoveTeam;

    /**
     * @param string $playerName
     * @return bool
     * @throws \Exception
     */
    public function canJoin(string $playerName): bool {
        $playerData = PlayerDataStore::getByName($playerName);
        if ($playerData->getBelongGameId() !== null) return false;

        $hasEmpty = false;
        foreach ($this->teams as $team) {
            if ($team->getMaxPlayer() === null) {
                $hasEmpty = true;
                break;
            }

            $teamPlayerDataList = PlayerDataStore::getTeamPlayerData($team->getId());
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

    public function getTeamById(TeamId $teamId): Team {
        foreach ($this->teams as $team) {
            if ($team->getId()->equals($teamId)) return $team;
        }

        throw new \Exception("そのIDのチームは存在しません");
    }

    public function getMaxPlayersDifference(): int {
        return $this->maxPlayersDifference;
    }

    public function isCanMoveTeam(): bool {
        return $this->canMoveTeam;
    }
}