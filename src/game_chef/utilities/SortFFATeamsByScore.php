<?php


namespace game_chef\utilities;


use game_chef\models\FFAPlayerTeam;
use game_chef\models\Team;

class SortFFATeamsByScore
{
    /**
     * @param FFAPlayerTeam[]  $teams
     * @return FFAPlayerTeam[]
     */
    static function sort(array $teams): array {
        usort($teams, function ($a, $b): int {
            return self::compare($a, $b);
        });

        return $teams;
    }


    //big to small
    /**
     * @param FFAPlayerTeam $a
     * @param FFAPlayerTeam $b
     * @return int
     */
    static private function compare($a, $b): int {
        if ($a->getScore()->equals($b->getScore())) {
            return 0;
        }
        return ($a->getScore()->isSmallerThan($b->getScore())) ? -1 : 1;
    }
}