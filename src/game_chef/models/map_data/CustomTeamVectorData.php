<?php


namespace game_chef\models\map_data;


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

    public function toJson(): array {
        return [
            "x" => $this->vector3->getX(),
            "y" => $this->vector3->getY(),
            "z" => $this->vector3->getZ(),
        ];
    }

    static function fromJson(array $json): CustomTeamVectorData {
        return new CustomTeamVectorData(
            $json["key"],
            $json["team_name"],
            new Vector3(
                $json["x"],
                $json["y"],
                $json["z"]
            )
        );
    }
}