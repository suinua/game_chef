<?php


namespace game_assistant\models;


class TeamGameMap extends Map
{
    /**
     * @var string[]
     */
    private array $teamNames;
    /**
     * @var TeamSpawnPointBunch[]
     */
    private array $teamSpawnPointBunches;

    //TODO:teamNamesとspawnPointGroupListが一致しなきゃダメ
    public function __construct(string $name, string $levelName, array $adaptedGameTypes, array $teamNames, array $teamSpawnPointBunches) {
        parent::__construct($name, $levelName, $adaptedGameTypes);
        $this->teamNames = $teamNames;
        $this->teamSpawnPointBunches = $teamSpawnPointBunches;
    }
}
