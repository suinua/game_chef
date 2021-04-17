<?php


namespace game_chef\models;


use game_chef\models\map_data\FFAGameMapData;
use pocketmine\math\Vector3;

class FFAGameMap extends Map
{
    /**
     * @var Vector3[]
     */
    private array $spawnPoints;

    public function __construct(string $name, string $levelName, array $customMapVectorDataList, array $customMapVectorsDataList, array $spawnPoints) {
        parent::__construct($name, $levelName, $customMapVectorDataList, $customMapVectorsDataList);
        $this->spawnPoints = $spawnPoints;
    }

    /**
     * @return Vector3[]
     */
    public function getSpawnPoints(): array {
        return $this->spawnPoints;
    }

    static function fromMapData(FFAGameMapData $ffaGameMapData): FFAGameMap {
        return new FFAGameMap(
            $ffaGameMapData->getName(),
            $ffaGameMapData->getLevelName(),
            $ffaGameMapData->getCustomMapVectorDataList(),
            $ffaGameMapData->getCustomMapArrayVectorDataList(),
            $ffaGameMapData->getSpawnPoints()
        );
    }
}