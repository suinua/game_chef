<?php


namespace game_chef\api;


use game_chef\models\map_data\TeamGameMapData;
use game_chef\models\Team;
use game_chef\models\TeamGame;
use game_chef\models\TeamGameMap;
use game_chef\repository\TeamGameMapDataRepository;
use game_chef\services\MapService;

//GoFのBuilderパターンではない
class TeamGameBuilder extends GameBuilder
{
    private ?TeamGameMap $map = null;
    private ?TeamGameMapData $mapData = null;
    private ?int $numberOfTeams = null;
    private bool $friendlyFire = false;
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
        if ($this->numberOfTeams !== null) throw new \Exception("NumberOfTeamsを再度セットすることは出来ません");
        $this->numberOfTeams = $numberOfTeams;
    }

    /**
     * @param string $mapName
     * @throws \Exception
     */
    public function selectMapByName(string $mapName): void {
        if ($this->mapData !== null) throw new \Exception("Mapを再度セットすることは出来ません");

        if ($this->gameType === null or $this->numberOfTeams === null) {
            throw new \Exception("GameTypeまたはチーム数より先にMapをセットすることは出来ません");
        }

        $this->mapData = TeamGameMapDataRepository::loadByName($mapName);
        $this->map = MapService::useTeamGameMap($mapName, $this->gameType, $this->numberOfTeams);
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
        foreach ($this->mapData->getTeamDataList() as $teamDataOnMap) {
            $existTeamName[] = $teamDataOnMap->getName();
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

        $teamDataOnMap = $this->mapData->getTeamData($teamName);
        $teamDataOnMap->setMaxPlayers($maxPlayer);
        $teamDataOnMap->setMaxPlayers($minPlayer);

        $this->useTeams[] = Team::fromTeamDataOnMap($teamDataOnMap);
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
        if ($this->map === null) throw new \Exception("Mapをセットしていない状態でゲームを作ることはできません");
        if ($this->gameType === null) throw new \Exception("GameTypeをセットしていない状態でゲームを作ることはできません");

        if ($this->numberOfTeams === null) throw new \Exception("NumberOfTeamsをセットしていない状態でゲームを作ることはできません");

        if ($this->useTeams === null) {
            $teams = [];
            $indexList = array_rand($this->mapData->getTeamDataList(), $this->numberOfTeams);
            foreach ($indexList as $index) {
                $teamDataOnMap = $this->mapData->getTeamDataList()[$index];
                $teams[] = Team::fromTeamDataOnMap($teamDataOnMap);
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