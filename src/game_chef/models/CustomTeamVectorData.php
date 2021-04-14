<?php


namespace game_chef\models;


use pocketmine\math\Vector3;

class CustomTeamVectorData extends CustomTeamData
{
    private Vector3 $vector3;

    public function __construct(string $key, string $teamName, Vector3 $vector3) {
        $this->vector3 = $vector3;
        parent::__construct($key, $teamName);
    }

    public function getVector3(): Vector3 {
        return $this->vector3;
    }
}