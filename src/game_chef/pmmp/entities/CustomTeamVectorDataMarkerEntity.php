<?php


namespace game_chef\pmmp\entities;



use game_chef\models\map_data\CustomTeamVectorData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
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


    private string $userName;
    private TeamGameMapData $belongMapData;
    private CustomTeamVectorData $customTeamVectorData;

    public function __construct(string $userName, TeamGameMapData $belongMapData, CustomTeamVectorData $customTeamVectorData, Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->userName = $userName;
        $this->belongMapData = $belongMapData;
        $this->customTeamVectorData = $customTeamVectorData;

        $vector = $customTeamVectorData->getVector3();
        $this->setNameTag("{$customTeamVectorData->getKey()} \n x:{$vector->getX()},y:{$vector->getY()},z:{$vector->getZ()}");
        $this->setNameTagAlwaysVisible(true);
    }

    public function getBelongMapData(): MapData {
        return $this->belongMapData;
    }

    public function getUserName(): string {
        return $this->userName;
    }

    public function onTap(Player $player): void {}
}