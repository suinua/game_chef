<?php


namespace game_assistant\utilities;


use game_assistant\models\Team;
use game_assistant\store\PlayerDataStore;

class SortTeamsByPlayers
{
    /**
     * @param Team[] $teams
     * @return Team[]
     */
    static function sort(array $teams): array {
        usort($teams, function ($a, $b): int {
            return self::compare($a, $b);
        });

        return $teams;
    }


    //small to big
    static private function compare(Team $a, Team $b): int {
        $teamAPlayersCount = count(PlayerDataStore::getTeamPlayerData($a->getId()));
        $teamBPlayersCount = count(PlayerDataStore::getTeamPlayerData($b->getId()));

        if ($teamAPlayersCount === $teamBPlayersCount) {
            return 0;
        }
        return ($teamAPlayersCount < $teamBPlayersCount) ? -1 : 1;
    }
}