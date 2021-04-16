<?php


namespace game_chef\pmmp\entities;


use game_chef\models\FFAGameMap;
use game_chef\pmmp\hotbar_menu\DeleteFFASpawnPointHotbarMenu;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

//TODO: テレポートできないように(座標を変えさせない)
class FFAGameMapSpawnPointMarkerEntity extends NPCBase
{
    const NAME = "FFAGameMapSpawnPointMarkerEntity";
    public string $skinName = "empty";
    protected string $geometryId = "geometry.empty";
    protected string $geometryName = "empty.geo.json";

    public $width = 1.0;
    public $height = 1.0;
    public $eyeHeight = 1.0;
    protected $gravity = 0;


    private string $userName;
    private FFAGameMap $belongMap;
    private Vector3 $mapSpawnPoint;

    public function __construct(string $userName, FFAGameMap $belongMap, Vector3 $mapSpawnPoint, Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->userName = $userName;
        $this->belongMap = $belongMap;
        $this->mapSpawnPoint = $mapSpawnPoint;
        $this->setNameTag("x:{$mapSpawnPoint->getX()},y:{$mapSpawnPoint->getY()},z:{$mapSpawnPoint->getZ()}");
        $this->setNameTagAlwaysVisible(true);
    }

    /**
     * @return FFAGameMap
     */
    public function getBelongMap(): FFAGameMap {
        return $this->belongMap;
    }

    /**
     * @return string
     */
    public function getUserName(): string {
        return $this->userName;
    }

    public function onTap(Player $player): void {
        $menu = new DeleteFFASpawnPointHotbarMenu($player, $this->belongMap, $this->mapSpawnPoint);
        $menu->send();
    }

    public function getMapSpawnPoint(): Vector3 {
        return $this->mapSpawnPoint;
    }
}