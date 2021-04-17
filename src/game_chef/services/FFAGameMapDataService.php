<?php


namespace game_chef\services;


use game_chef\models\map_data\FFAGameMapData;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\store\MapsStore;

class FFAGameMapDataService
{
    /**
     * @param string $name
     * @param string $levelName
     * @param array $gameTypeList
     * @throws \Exception
     */
    static function create(string $name, string $levelName, array $gameTypeList): void {
        $map = new FFAGameMapData($name, $levelName, $gameTypeList, [], [], []);
        FFAGameMapDataRepository::add($map);
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    static function delete(string $name): void {
        if (in_array($name, MapsStore::getLoanOutTeamGameMapName())) {
            throw new \Exception("使用中のマップは削除できません");
        }

        FFAGameMapDataRepository::delete($name);
    }

    /**
     * @param FFAGameMapData $ffaGameMapData
     * @throws \Exception
     */
    static function update(FFAGameMapData $ffaGameMapData): void {
        if (in_array($ffaGameMapData->getName(), MapsStore::getLoanOutTeamGameMapName())) {
            throw new \Exception("使用中のマップは編集できません");
        }

        FFAGameMapDataRepository::update($ffaGameMapData);
    }
}