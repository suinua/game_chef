<?php


namespace game_chef\pmmp\entities;


use game_chef\models\map_data\CustomMapVectorData;
use pocketmine\Player;

class CustomMapVectorDataMarkerEntity extends NPCBase
{
    const NAME = "CustomMapVectorDataMarkerEntity";
    public string $skinName = "empty";
    protected string $geometryId = "geometry.empty";
    protected string $geometryName = "empty.geo.json";

    public $width = 1.0;
    public $height = 1.0;
    public $eyeHeight = 1.0;
    protected $gravity = 0;

    static function create(Player $player, CustomMapVectorData $customMapVectorData) : self {
        $nbt = self::createBaseNBT($customMapVectorData->getVector3()->add(0.5, 1.3, 0.5));
        $vector3 = $customMapVectorData->getVector3();

        $entity = new self($player->getLevel(), $nbt);
        $entity->setNameTagAlwaysVisible(true);
        $entity->setNameTag("{$customMapVectorData->getKey()}\nx:{$vector3->getX()},y:{$vector3->getY()},z:{$vector3->getZ()}");
        return $entity;
    }

    public function getBelongMapName(): string {
        return $this->namedtag->getString("map_name");
    }

    public function onTap(Player $player): void {}
}