<?php


namespace game_chef\pmmp\entities;


use game_chef\models\map_data\FFAGameMapData;
use game_chef\pmmp\hotbar_menu\DeleteFFASpawnPointHotbarMenu;
use game_chef\repository\FFAGameMapDataRepository;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
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

    static function create(Player $player, FFAGameMapData $belongMapData, Vector3 $mapSpawnPoint) : self {
        $nbt = Entity::createBaseNBT($mapSpawnPoint->add(0.5, 1.3, 0.5));
        $nbt->setString("map_name", $belongMapData->getName());
        $nbt->setIntArray("spawn_point", [$mapSpawnPoint->getX(), $mapSpawnPoint->getY(), $mapSpawnPoint->getZ()]);

        $entity = new self($player->getLevel(), $nbt);
        $entity->setNameTagAlwaysVisible(true);
        $entity->setNameTag("x:{$mapSpawnPoint->getX()},y:{$mapSpawnPoint->getY()},z:{$mapSpawnPoint->getZ()}");
        return $entity;
    }

    public function getBelongMapName(): string {
        return $this->namedtag->getString("map_name");
    }

    public function onTap(Player $player): void {
        $belongMapData = FFAGameMapDataRepository::loadByName($this->namedtag->getString("map_name"));
        $array = $this->namedtag->getIntArray("spawn_point");
        $mapSpawnPoint = new Vector3($array[0], $array[1], $array[2]);

        $menu = new DeleteFFASpawnPointHotbarMenu($player, $belongMapData, $mapSpawnPoint);
        $menu->send();
    }
}