<?php


namespace game_chef\pmmp\entities;


use game_chef\models\TeamDataOnMap;
use game_chef\models\TeamGameMap;
use game_chef\pmmp\hotbar_menu\DeleteTeamSpawnPointHotbarMenu;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class TeamGameMapSpawnPointMarkerEntity extends NPCBase
{
    const NAME = "TeamGameMapSpawnPointMarkerEntity";
    public string $skinName = "empty";
    protected string $geometryId = "geometry.empty";
    protected string $geometryName = "empty.geo.json";

    public $width = 1.0;
    public $height = 1.0;
    public $eyeHeight = 1.0;
    protected $gravity = 0;


    private string $userName;
    private TeamGameMap $belongMap;
    private TeamDataOnMap $teamData;
    private Vector3 $mapSpawnPoint;

    public function __construct(string $userName, TeamGameMap $belongMap, TeamDataOnMap $teamDataOnMap, Vector3 $mapSpawnPoint, Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->userName = $userName;
        $this->belongMap = $belongMap;
        $this->teamData = $teamDataOnMap;
        $this->mapSpawnPoint = $mapSpawnPoint;
        $this->setNameTag("x:{$mapSpawnPoint->getX()},y:{$mapSpawnPoint->getY()},z:{$mapSpawnPoint->getZ()}");
        $this->setNameTagAlwaysVisible(true);
    }

    /**
     * @return TeamGameMap
     */
    public function getBelongMap(): TeamGameMap {
        return $this->belongMap;
    }

    /**
     * @return string
     */
    public function getUserName(): string {
        return $this->userName;
    }

    public function onTap(Player $player): void {
        $menu = new DeleteTeamSpawnPointHotbarMenu($player, $this->belongMap, $this->teamData, $this->mapSpawnPoint);
        $menu->send();
    }

    public function getMapSpawnPoint(): Vector3 {
        return $this->mapSpawnPoint;
    }
}