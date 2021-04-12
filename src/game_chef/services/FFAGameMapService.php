<?php


namespace game_chef\services;


use game_chef\models\FFAGameMap;
use game_chef\models\Map;
use game_chef\repository\FFAGameMapRepository;
use game_chef\store\MapsStore;
use pocketmine\math\Vector3;

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

    /**
     * @param FFAGameMap $ffaGameMap
     * @throws \Exception
     */
    static function update(FFAGameMap $ffaGameMap): void {
        if (in_array($ffaGameMap->getName(), MapsStore::getLoanOutFFAGameMapNames())) {
            throw new \Exception("使用中のマップは編集できません");
        }

        FFAGameMapRepository::update($ffaGameMap);
    }

    /**
     * @param FFAGameMap $map
     * @param Vector3 $vector3
     * @throws \Exception
     */
    static function deleteSpawnPoint(FFAGameMap $map, Vector3 $vector3): void {
        $newSpawnPoints = [];
        foreach ($map->getSpawnPoints() as $spawnPoint) {
            if (!$spawnPoint->equals($vector3)) {
                $newSpawnPoints[] = $spawnPoint;
            }
        }

        self::update(
            new FFAGameMap(
                $map->getName(),
                $map->getLevelName(),
                $map->getAdaptedGameTypes(),
                $newSpawnPoints
            )
        );
    }

    /**
     * @param FFAGameMap $map
     * @param Vector3 $vector3
     * @throws \Exception
     */
    static function addSpawnPoint(FFAGameMap $map, Vector3 $vector3): void {
        $spawnPoints = $map->getSpawnPoints();
        foreach ($spawnPoints as $spawnPoint) {
            if ($spawnPoint->equals($vector3)) {
                throw new \Exception("FFAGameMapでは同じ座標に２つ以上スポーン地点を追加することはできません");
            }
        }
        $spawnPoints[] = $vector3;

        self::update(
            new FFAGameMap(
                $map->getName(),
                $map->getLevelName(),
                $map->getAdaptedGameTypes(),
                $spawnPoints
            )
        );
    }
}