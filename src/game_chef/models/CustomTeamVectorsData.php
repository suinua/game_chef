<?php


namespace game_chef\models;


use pocketmine\math\Vector3;

class CustomTeamVectorsData extends CustomTeamData
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
}