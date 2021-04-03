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

    public function canJoin(string $playerName): bool {
        try {
            $playerData = PlayerDataStore::getByName($playerName);
        } catch (\Exception $exception) {
            //TODO:メッセージ
            return false;
        }
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

}