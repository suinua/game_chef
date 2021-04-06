<?php


namespace game_chef\models;


class TeamGameMap extends Map
{
    /**
     * @var TeamDataOnMap[]
     * $teamName => TeamDataOnMap
     */
    private array $teamDataList;

    /**
     * TeamGameMap constructor.
     * @param string $name
     * @param string $levelName
     * @param GameType[] $adaptedGameTypes
     * @param TeamDataOnMap[] $teamDataList
     */
    public function __construct(string $name, string $levelName, array $adaptedGameTypes, array $teamDataList) {
        parent::__construct($name, $levelName, $adaptedGameTypes);
        $this->teamDataList = [];
        foreach ($teamDataList as $teamData) {
            $this->teamDataList[$teamData->getTeamName()] = $teamData;
        }
    }

    /**
     * @return TeamDataOnMap[]
     */
    public function getTeamDataList(): array {
        return array_values($this->teamDataList);
    }

    /**
     * @param string $teamName
     * @return TeamDataOnMap
     * @throws \Exception
     */
    public function getTeamDataOnMapByName(string $teamName): TeamDataOnMap {
        if (!array_key_exists($teamName,$this->teamDataList)) {
            throw new \Exception("そのチームデータ($teamName)はこのマップ({$this->name})に登録されていません");
        }
        return $this->teamDataList[$teamName];
    }
}
