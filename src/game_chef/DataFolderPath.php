<?php


namespace game_chef;


class DataFolderPath
{
    static string $maps;
    static string $teamGameMaps;
    static string $ffaGameMaps;

    static string $skin;
    static string $geometry;

    static function init(string $dataPath, string $resourcePath) {
        self::$maps = $dataPath . "maps/";
        if (!file_exists(self::$maps)) mkdir(self::$maps);

        self::$teamGameMaps = self::$maps . "team_game_maps/";
        if (!file_exists(self::$teamGameMaps)) mkdir(self::$teamGameMaps);
        self::$ffaGameMaps = self::$maps . "ffa_game_maps/";
        if (!file_exists(self::$ffaGameMaps)) mkdir(self::$ffaGameMaps);

        self::$skin = $resourcePath . "skin/";
        if (!file_exists(self::$skin)) mkdir(self::$skin);

        self::$geometry = $resourcePath . "geometry/";
        if (!file_exists(self::$geometry)) mkdir(self::$geometry);
    }
}