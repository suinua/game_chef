<?php


namespace game_chef\pmmp\entities;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

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

    public function __construct(string $userName, string $mapName, Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->userName = $userName;
        $this->belongMapName = $mapName;
        $this->setInvisible(true);
        $this->setNameTag("x:{$this->getX()},y:{$this->getY()},z:{$this->getZ()}");
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
}