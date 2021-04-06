<?php


namespace game_chef;


use game_chef\models\Team;
use game_chef\models\TeamGame;
use game_chef\models\TeamGameMap;
use game_chef\store\MapsStore;

//GoFのBuilderパターンではない
class TeamGameBuilder extends GameBuilder
{
    private TeamGameMap $map;
    private int $numberOfTeams;
    private bool $friendlyFire;
    private ?int $maxPlayersDifference = null;
    private bool $canMoveTeam = false;

    /**
     * @var Team[]|null
     * 設定しない場合、$maxPlayerと$minPlayerはマップに登録されたデフォルト値になり $numberOfTeamsよりランダムで選択されます
     */
    private ?array $useTeams = null;

    public function __construct() { }

    /**
     * @param int $numberOfTeams
     * @throws \Exception
     */
    public function setNumberOfTeams(int $numberOfTeams): void {
        if ($this->numberOfTeams !== null) throw new \Exception("再度セットすることは出来ません");
        $this->numberOfTeams = $numberOfTeams;
    }

    /**
     * @param string $mapName
     * @throws \Exception
     */
    public function selectMapByName(string $mapName): void {
        if ($this->gameType === null or $this->numberOfTeams === null) {
            throw new \Exception("GameTypeまたはチーム数より先にセットすることは出来ません");
        }

        $this->map = MapsStore::borrowTeamGameMap($mapName, $this->gameType, $this->numberOfTeams);
    }

    /**
     * @param string $teamName
     * @param int|null $maxPlayer
     * @param int|null $minPlayer
     * @throws \Exception
     */
    public function setUpTeam(string $teamName, ?int $maxPlayer = null, ?int $minPlayer = null): void {
        if ($this->map === null) {
            throw new \Exception("Mapより先にセットすることは出来ません");
        }

        $existTeamName = [];
        foreach ($this->map->getTeamDataList() as $teamDataOnMap) {
            $existTeamName[] = $teamDataOnMap->getTeamName();
        }

        if (!in_array($teamName, $existTeamName)) {
            throw new \Exception("Mapに登録されてないチーム名は使用することができません");
        }

        if ($this->useTeams !== null) {
            foreach ($this->useTeams as $useTeam) {
                if ($useTeam->getName() === $teamName) {
                    throw new \Exception("同じチーム名($teamName)は使用することができません");
                }
            }
        }

        $teamDataOnMap = $this->map->getTeamDataOnMapByName($teamName);
        $this->useTeams[] = new Team($teamDataOnMap->getTeamName(), $teamDataOnMap->getSpawnPoints(), $teamDataOnMap->getTeamColorFormat(), $maxPlayer, $minPlayer);
    }

    public function setFriendlyFire(bool $friendlyFire): void {
        $this->friendlyFire = $friendlyFire;
    }

    public function setMaxPlayersDifference(?int $difference): void {
        $this->maxPlayersDifference = $difference;
    }

    public function setCanMoveTeam(bool $canMoveTeam): void {
        $this->canMoveTeam = $canMoveTeam;
    }

    /**
     * @return TeamGame
     * @throws \Exception
     */
    public function build(): TeamGame {
        //TODO:FFAGameBuilderと共通
        if ($this->map === null) throw new \Exception("mapをセットしていない状態でゲームを作ることはできません");
        if ($this->gameType === null) throw new \Exception("gameTypeをセットしていない状態でゲームを作ることはできません");

        if ($this->numberOfTeams === null) throw new \Exception("numberOfTeamsをセットしていない状態でゲームを作ることはできません");

        if ($this->useTeams === null) {
            $teams = [];
            $indexList = array_rand($this->map->getTeamDataList(), $this->numberOfTeams);
            foreach ($indexList as $index) {
                $teamDataOnMap = $this->map->getTeamDataList()[$index];
                $teams[] = new Team($teamDataOnMap->getTeamName(), $teamDataOnMap->getSpawnPoints(), $teamDataOnMap->getTeamColorFormat(), $teamDataOnMap->getMaxPlayer(), $teamDataOnMap->getMinPlayer());
            }
        } else {
            $teams = $this->useTeams;
        }

        return new TeamGame(
            $this->map,
            $this->gameType,
            $this->victoryScore,
            $this->canJumpIn,
            $this->timeLimit,
            $teams,
            $this->friendlyFire,
            $this->maxPlayersDifference,
            $this->canMoveTeam,
        );
    }

}