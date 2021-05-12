<?php


namespace game_chef\pmmp\entities;


use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\hotbar_menu\DeleteTeamSpawnPointHotbarMenu;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\math\Vector3;
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

    static function create(Player $player, TeamGameMapData $belongMapData, TeamDataOnMap $teamDataOnMap, Vector3 $mapSpawnPoint) : self {
        $nbt = self::createBaseNBT($mapSpawnPoint->add(0.5, 1.3, 0.5));
        $nbt->setString("team_name", strval($teamDataOnMap->getName()));
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
        $belongMapData = TeamGameMapDataRepository::loadByName($this->namedtag->getString("map_name"));
        $teamData = $belongMapData->getTeamData($this->namedtag->getString("team_name"));
        $array = $this->namedtag->getIntArray("spawn_point");
        $mapSpawnPoint = new Vector3($array[0], $array[1], $array[2]);

        $menu = new DeleteTeamSpawnPointHotbarMenu($player, $belongMapData, $teamData, $mapSpawnPoint);
        $menu->send();
    }
}