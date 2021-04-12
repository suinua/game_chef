<?php


namespace game_chef\pmmp\entities;


use game_chef\pmmp\hotbar_menu\DeleteFFASpawnPointHotbarMenu;
use game_chef\pmmp\hotbar_menu\EditFFAGameSpawnPointsHotbarMenu;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

//TODO: テレポートできないように(座標を変えさせない)
class FFAGameMapSpawnPointMarkerEntity extends NPCBase
{
    const NAME = "FFAGameMapSpawnPointMarkerEntity";
    public string $skinName = self::NAME;
    protected string $geometryId = "geometry." . self::NAME;
    protected string $geometryName = self::NAME . ".geo.json";

    public $width = 1.0;
    public $height = 2.0;
    public $eyeHeight = 2.0;
    protected $gravity = 0;


    private string $userName;
    private string $belongMapName;
    private Vector3 $mapSpawnPoint;

    public function __construct(string $userName, string $mapName, Vector3 $mapSpawnPoint, Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->userName = $userName;
        $this->belongMapName = $mapName;
        $this->mapSpawnPoint = $mapSpawnPoint;
        $this->setInvisible(true);
        $this->setNameTag("x:{$mapSpawnPoint->getX()},y:{$mapSpawnPoint->getY()},z:{$mapSpawnPoint->getZ()}");
    }

    /**
     * @return string
     */
    public function getBelongMapName(): string {
        return $this->belongMapName;
    }

    /**
     * @return string
     */
    public function getUserName(): string {
        return $this->userName;
    }

    public function onTap(Player $player): void {
        $menu = new DeleteFFASpawnPointHotbarMenu($player, $this->belongMapName, $this->mapSpawnPoint);
        $menu->send();
    }
}