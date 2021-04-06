<?php


namespace game_chef;


class DataFolderPath
{
    static string $maps;
    static string $teamGameMaps;
    static string $ffaGameMaps;

    static function init(string $dataPath) {
        self::$maps = $dataPath . "maps/";
        if (!file_exists(self::$maps)) mkdir(self::$maps);

        self::$teamGameMaps = self::$maps . "team_game_maps/";
        self::$ffaGameMaps = self::$maps . "ffa_game_maps/";
    }
}