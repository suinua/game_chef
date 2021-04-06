<?php


namespace game_chef;


class DataFolderPath
{
    static string $map;
    static function init(string $dataPath) {
        self::$map = $dataPath . "maps/";
        if (!file_exists(self::$map)) mkdir(self::$map);
    }
}