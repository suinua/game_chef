<?php


namespace game_chef\models\editors;


use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\entities\TeamGameMapSpawnPointMarkerEntity;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class TeamGameMapSpawnPointEditor extends Editor
{
    private TeamDataOnMap $teamData;

    public function __construct(TeamGameMapData $mapData, TeamDataOnMap $teamData, Player $user, TaskScheduler $scheduler) {
        parent::__construct($mapData, $user, $scheduler);
        $this->teamData = $teamData;
    }


    /**
     * @throws \Exception
     */
    public function reloadMap(): void {
        $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
        $this->teamData = $this->mapData->getTeamData($this->teamData->getName());

        parent::reloadMap();
    }

    /**
     * @throws \Exception
     */
    public function start(): void {
        parent::start();

        $level = Server::getInstance()->getLevelByName($this->mapData->getLevelName());

        foreach ($this->teamData->getSpawnPoints() as $spawnPoint) {
            $this->summonMarkerEntity($level, $spawnPoint);
        }

        $this->handler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($level): void {
            foreach ($this->teamData->getSpawnPoints() as $spawnPoint) {
                $this->summonParticle($level, $spawnPoint);
            }
        }), 10);
    }

    protected function summonMarkerEntity(Level $level, Vector3 $vector3): void {
        if ($this->mapData instanceof TeamGameMapData) {
            $marker = TeamGameMapSpawnPointMarkerEntity::create($this->user, $this->mapData, $this->teamData, $vector3);
            $marker->spawnTo($this->user);
        }
    }

    protected function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof TeamGameMapSpawnPointMarkerEntity) {
                if ($entity->getBelongMapName() === $this->mapData->getName()) $entity->kill();
            }
        }
    }
}