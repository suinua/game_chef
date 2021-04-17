<?php


namespace game_chef\models\map_data;


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

    public function toJson(): array {
        return [
            "x" => $this->vector3->getX(),
            "y" => $this->vector3->getY(),
            "z" => $this->vector3->getZ(),
        ];
    }

    static function fromJson(array $json): CustomMapVectorData {
        return new CustomMapVectorData(
            $json["key"],
            new Vector3(
                $json["x"],
                $json["y"],
                $json["z"]
            )
        );
    }
}