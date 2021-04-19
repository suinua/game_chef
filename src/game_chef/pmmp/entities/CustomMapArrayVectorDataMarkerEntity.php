<?php


namespace game_chef\pmmp\entities;


use game_chef\models\map_data\CustomMapArrayVectorData;
use game_chef\models\map_data\CustomMapVectorData;
use game_chef\models\map_data\MapData;
use game_chef\pmmp\hotbar_menu\DeleteCustomMapArrayVectorDataHotbarMenu;
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


    private string $userName;
    private MapData $belongMapData;
    private CustomMapArrayVectorData $customMapArrayVectorData;
    private Vector3 $vector3;

    public function __construct(string $userName, MapData $belongMapData, CustomMapArrayVectorData $customMapArrayVectorData, Vector3 $vector3, Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->userName = $userName;
        $this->belongMapData = $belongMapData;
        $this->customMapArrayVectorData = $customMapArrayVectorData;
        $this->vector3 = $vector3;

        $this->setNameTag("x:{$vector3->getX()},y:{$vector3->getY()},z:{$vector3->getZ()}");
        $this->setNameTagAlwaysVisible(true);
    }

    public function getBelongMapData(): MapData {
        return $this->belongMapData;
    }

    public function getUserName(): string {
        return $this->userName;
    }

    public function onTap(Player $player): void {
        $menu = new DeleteCustomMapArrayVectorDataHotbarMenu($player, $this->belongMapData, $this->customMapArrayVectorData);
        $menu->send();
    }

    public function getVector3(): Vector3 {
        return $this->vector3;
    }
}