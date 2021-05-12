<?php


namespace game_chef\pmmp\entities;


use game_chef\models\map_data\CustomTeamArrayVectorData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\hotbar_menu\DeleteCustomTeamArrayVectorDataHotbarMenu;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\math\Vector3;
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

    static function create(Player $player, TeamGameMapData $belongMapData, TeamDataOnMap $teamDataOnMap, CustomTeamArrayVectorData $customTeamArrayVectorData, Vector3 $vector3) : self {
        $nbt = self::createBaseNBT($vector3->add(0.5, 1.3, 0.5));
        $nbt->setString("team_name", $teamDataOnMap->getName());
        $nbt->setString("map_name", $belongMapData->getName());
        $nbt->setString("key", $customTeamArrayVectorData->getKey());
        $nbt->setIntArray("vector", [$vector3->getX(), $vector3->getY(), $vector3->getZ()]);

        $entity = new self($player->getLevel(), $nbt);
        $entity->setNameTagAlwaysVisible(true);
        $entity->setNameTag("x:{$vector3->getX()},y:{$vector3->getY()},z:{$vector3->getZ()}");
        return $entity;
    }

    public function getBelongMapName(): string {
        return $this->namedtag->getString("map_name");
    }

    public function onTap(Player $player): void {
        $belongMapData = TeamGameMapDataRepository::loadByName($this->namedtag->getString("map_name"));
        $teamData = $belongMapData->getTeamData($this->namedtag->getString("team_name"));
        $array = $this->namedtag->getIntArray("vector");
        $customTeamArrayVectorData = $teamData->getCustomArrayVectorData($this->namedtag->getString("key"));
        $vector3 = new Vector3($array[0], $array[1], $array[2]);


        $menu = new DeleteCustomTeamArrayVectorDataHotbarMenu($player, $belongMapData, $teamData, $customTeamArrayVectorData, $vector3);
        $menu->send();
    }
}