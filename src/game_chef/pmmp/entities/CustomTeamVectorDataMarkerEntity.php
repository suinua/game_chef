<?php


namespace game_chef\pmmp\entities;



use game_chef\models\map_data\CustomTeamVectorData;
use pocketmine\Player;

class CustomTeamVectorDataMarkerEntity extends NPCBase
{
    const NAME = "CustomMapVectorDataMarkerEntity";
    public string $skinName = "empty";
    protected string $geometryId = "geometry.empty";
    protected string $geometryName = "empty.geo.json";

    public $width = 1.0;
    public $height = 1.0;
    public $eyeHeight = 1.0;
    protected $gravity = 0;

    static function create(Player $player, CustomTeamVectorData $customTeamVectorData) : self {
        $nbt = self::createBaseNBT($customTeamVectorData->getVector3()->add(0.5, 1.3, 0.5));
        $vector3 = $customTeamVectorData->getVector3();
        $nbt->setIntArray("vector", [$vector3->getX(), $vector3->getY(), $vector3->getZ()]);

        $entity = new self($player->getLevel(), $nbt);
        $entity->setNameTagAlwaysVisible(true);
        $entity->setNameTag("{$customTeamVectorData->getKey()} \nx:{$vector3->getX()},y:{$vector3->getY()},z:{$vector3->getZ()}");
        return $entity;
    }

    public function getBelongMapName(): string {
        return $this->namedtag->getString("map_name");
    }

    public function onTap(Player $player): void {}
}