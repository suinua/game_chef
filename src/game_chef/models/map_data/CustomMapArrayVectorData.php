<?php


namespace game_chef\models\map_data;


use pocketmine\math\Vector3;

class CustomMapArrayVectorData extends CustomMapData
{
    /**
     * @var Vector3[]
     */
    private array $vector3List;

    public function __construct(string $key, array $vector3List) {
        $this->vector3List = $vector3List;
        parent::__construct($key);
    }

    /**
     * @return Vector3[]
     */
    public function getVector3List(): array {
        return $this->vector3List;
    }

    public function toJson(): array {
        $vectors = [];
        foreach ($this->vector3List as $vector3) {
            $vectors[] = [
                "x" => $vector3->getX(),
                "y" => $vector3->getY(),
                "z" => $vector3->getZ(),
            ];
        }

        return [
            "key" => $this->key,
            "vectors" => $vectors
        ];
    }

    static function fromJson(array $json): CustomMapArrayVectorData {
        $vectors = [];
        foreach ($json["vectors"] as $vector) {
            $vectors[] = new Vector3($vector["x"], $vector["y"], $vector["z"]);
        }

        return new CustomMapArrayVectorData($json["key"], $vectors);
    }
}