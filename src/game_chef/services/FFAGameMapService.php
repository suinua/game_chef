<?php


namespace game_chef\services;


use game_chef\models\FFAGameMap;
use game_chef\repository\FFAGameMapRepository;
use game_chef\store\MapsStore;

class FFAGameMapService
{
    /**
     * @param string $name
     * @param string $levelName
     * @param array $gameTypeList
     * @throws \Exception
     */
    static function create(string $name, string $levelName, array $gameTypeList): void {
        $map = new FFAGameMap($name, $levelName, $gameTypeList, []);
        FFAGameMapRepository::add($map);
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    static function delete(string $name): void {
        if (in_array($name, MapsStore::getLoanOutFFAGameMapNames())) {
            throw new \Exception("使用中のマップは削除できません");
        }

        FFAGameMapRepository::delete($name);
    }
}