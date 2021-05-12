<?php


namespace game_chef\pmmp\entities;


use game_chef\models\map_data\CustomMapArrayVectorData;
use game_chef\models\map_data\CustomTeamArrayVectorData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\hotbar_menu\DeleteCustomMapArrayVectorDataHotbarMenu;
use game_chef\repository\FFAGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class CustomMapArrayVectorDataMarkerEntity extends NPCBase
{
    const NAME = "CustomMapArrayVectorDataMarkerEntity";
    public string $skinName = "empty";
    protected string $geometryId = "geometry.empty";
    protected string $geometryName = "empty.geo.json";

    public $width = 1.0;
    public $height = 1.0;
    public $eyeHeight = 1.0;
    protected $gravity = 0;

    static function create(Player $player, MapData $belongMapData, CustomMapArrayVectorData $customMapArrayVectorData, Vector3 $vector3) : self {
        $nbt = self::createBaseNBT($vector3->add(0.5, 1.3, 0.5));
        $nbt->setString("map_name", $belongMapData->getName());
        $nbt->setString("key", $customMapArrayVectorData->getKey());

        $entity = new self($player->getLevel(), $nbt);
        $entity->setNameTagAlwaysVisible(true);
        $entity->setNameTag("x:{$vector3->getX()},y:{$vector3->getY()},z:{$vector3->getZ()}");
        return $entity;
    }

    public function getBelongMapName(): string {
        return $this->namedtag->getString("map_name");
    }

    public function onTap(Player $player): void {
        $belongMapData = FFAGameMapDataRepository::loadByName($this->namedtag->getString("map_name"));
        $customMapArrayVectorData = $belongMapData->getCustomMapArrayVectorData($this->namedtag->getString("key"));

        $menu = new DeleteCustomMapArrayVectorDataHotbarMenu($player, $belongMapData, $customMapArrayVectorData);
        $menu->send();
    }
}