<?php


namespace game_chef\models\editors;


use game_chef\models\map_data\CustomTeamArrayVectorData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\entities\CustomTeamArrayVectorDataMarkerEntity;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class CustomTeamArrayVectorDataEditor extends Editor
{
    private TeamDataOnMap $teamData;
    private CustomTeamArrayVectorData $customTeamArrayVectorData;

    public function __construct(TeamGameMapData $mapData, TeamDataOnMap $teamData, CustomTeamArrayVectorData $customTeamArrayVectorData, Player $user, TaskScheduler $scheduler) {
        parent::__construct($mapData, $user, $scheduler);
        $this->teamData = $teamData;
        $this->customTeamArrayVectorData = $customTeamArrayVectorData;
    }

    /**
     * @throws \Exception
     */
    public function reloadMap(): void {
        $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
        $this->teamData = $this->mapData->getTeamData($this->teamData->getName());
        $this->customTeamArrayVectorData = $this->teamData->getCustomArrayVectorData($this->customTeamArrayVectorData->getKey());

        parent::reloadMap();
    }

    /**
     * @throws \Exception
     */
    public function start(): void {
        parent::start();

        $level = Server::getInstance()->getLevelByName($this->mapData->getLevelName());
        foreach ($this->customTeamArrayVectorData->getVector3List() as $vector3) {
            $this->summonMarkerEntity($level, $vector3);
        }

        $this->handler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($level): void {
            foreach ($this->customTeamArrayVectorData->getVector3List() as $vector3) {
                $this->summonParticle($level, $vector3);
            }
        }), 10);
    }

    protected function summonMarkerEntity(Level $level, Vector3 $vector3): void {
        if ($this->mapData instanceof TeamGameMapData) {
            $marker = CustomTeamArrayVectorDataMarkerEntity::create($this->user, $this->mapData, $this->teamData, $this->customTeamArrayVectorData, $vector3);
            $marker->spawnTo($this->user);
        }
    }

    protected function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof CustomTeamArrayVectorDataMarkerEntity) {
                if ($entity->getBelongMapName() === $this->mapData->getName()) $entity->kill();
            }
        }
    }
}