<?php


namespace game_chef\models;


use pocketmine\math\Vector3;

class CustomMapVectorData extends CustomMapData
{
    private Vector3 $vector3;

    public function __construct(string $key, Vector3 $vector3) {
        $this->vector3 = $vector3;
        parent::__construct($key);
    }

    public function getVector3(): Vector3 {
        return $this->vector3;
    }
}