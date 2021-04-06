<?php


namespace game_chef\utilities;


use game_chef\models\Team;
use game_chef\store\PlayerDataStore;

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
        $teamAPlayersCount = count(PlayerDataStore::getByTeamId($a->getId()));
        $teamBPlayersCount = count(PlayerDataStore::getByTeamId($b->getId()));

        if ($teamAPlayersCount === $teamBPlayersCount) {
            return 0;
        }
        return ($teamAPlayersCount < $teamBPlayersCount) ? -1 : 1;
    }
}