<?php


namespace game_chef\pmmp\entities;


use game_chef\models\map_data\CustomTeamArrayVectorData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\hotbar_menu\DeleteCustomTeamArrayVectorDataHotbarMenu;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class CustomTeamArrayVectorDataMarkerEntity extends NPCBase
{
    const NAME = "CustomTeamArrayVectorDataMarkerEntity";
    public string $skinName = "empty";
    protected string $geometryId = "geometry.empty";
    protected string $geometryName = "empty.geo.json";

    public $width = 1.0;
    public $height = 1.0;
    public $eyeHeight = 1.0;
    protected $gravity = 0;


    private string $userName;
    private TeamGameMapData $belongMapData;
    private TeamDataOnMap  $teamData;
    private CustomTeamArrayVectorData $customTeamArrayVectorData;
    private Vector3 $vector3;

    public function __construct(string $userName, TeamGameMapData $belongMapData, TeamDataOnMap $teamData, CustomTeamArrayVectorData $customTeamArrayVectorData, Vector3 $vector3, Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->userName = $userName;
        $this->belongMapData = $belongMapData;
        $this->teamData = $teamData;
        $this->customTeamArrayVectorData = $customTeamArrayVectorData;
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
        $menu = new DeleteCustomTeamArrayVectorDataHotbarMenu($player, $this->belongMapData, $this->teamData, $this->customTeamArrayVectorData, $this->vector3);
        $menu->send();
    }

    public function getVector3(): Vector3 {
        return $this->vector3;
    }
}