<?php


namespace game_chef\utilities;


use game_chef\models\GameType;

class GameTypeListFromString
{
    static function execute(string $string): array {
        $gameTypeList = [];
        foreach (explode(",", $string) as $value) {
            if ($value === "") continue;
            $gameTypeList[] = new GameType($value);
        }

        return $gameTypeList;
    }
}