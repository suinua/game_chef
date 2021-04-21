<?php


namespace game_chef;


class DataFolderPath
{
    static string $worlds;

    static string $maps;
    static string $teamGameMaps;
    static string $ffaGameMaps;

    static string $skin;
    static string $geometry;

    static function init(string $dataPath, string $resourcePath, string $serverDataPath) {
        self::$worlds = $serverDataPath . "worlds" . DIRECTORY_SEPARATOR;

        self::$maps = $dataPath . "maps" . DIRECTORY_SEPARATOR;
        if (!file_exists(self::$maps)) mkdir(self::$maps);

        self::$teamGameMaps = self::$maps . "team_game_maps" . DIRECTORY_SEPARATOR;
        if (!file_exists(self::$teamGameMaps)) mkdir(self::$teamGameMaps);
        self::$ffaGameMaps = self::$maps . "ffa_game_maps" . DIRECTORY_SEPARATOR;
        if (!file_exists(self::$ffaGameMaps)) mkdir(self::$ffaGameMaps);

        self::$skin = $resourcePath . "skin" . DIRECTORY_SEPARATOR;
        if (!file_exists(self::$skin)) mkdir(self::$skin);

        self::$geometry = $resourcePath . "geometry" . DIRECTORY_SEPARATOR;
        if (!file_exists(self::$geometry)) mkdir(self::$geometry);
    }
}