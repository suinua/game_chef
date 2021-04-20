<?php


namespace game_chef\models\map_data;


use pocketmine\math\Vector3;

class CustomTeamArrayVectorData extends CustomTeamData
{
    /**
     * @var Vector3[]
     */
    private array $vector3List;

    public function __construct(string $key, string $teamName, array $vector3List) {
        $this->vector3List = $vector3List;
        parent::__construct($key, $teamName);
    }

    /**
     * @return Vector3[]
     */
    public function getVector3List(): array {
        return $this->vector3List;
    }

    /**
     * @param Vector3 $target
     * @throws \Exception
     */
    public function addVector3(Vector3 $target) {
        foreach ($this->vector3List as $vector3) {
            if ($vector3->equals($target)) {
                throw new \Exception("同じ座標を追加することはできません");
            }
        }

        $this->vector3List[] = $target;
    }

    /**
     * @param Vector3 $target
     * @throws \Exception
     */
    public function deleteVector3(Vector3 $target) {
        $isExist = false;
        $newList = [];
        foreach ($this->vector3List as $vector3) {
            if ($vector3->equals($target)) {
                $isExist = true;
            } else {
                $newList[] = $vector3;
            }
        }

        if (!$isExist) {
            throw new \Exception("存在しない座標を削除することはできません");
        }

        $this->vector3List = array_values($newList);
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
            "team_name" => $this->teamName,
            "vectors" => $vectors
        ];
    }

    static function fromJson(array $json): CustomTeamArrayVectorData {
        $vectors = [];
        foreach ($json["vectors"] as $vector) {
            $vectors[] = new Vector3($vector["x"], $vector["y"], $vector["z"]);
        }

        return new CustomTeamArrayVectorData($json["key"], $json["team_name"], $vectors);
    }
}