<?php


namespace game_chef\models\editors;


use game_chef\models\map_data\CustomTeamVectorData;
use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\entities\CustomTeamVectorDataMarkerEntity;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class CustomTeamVectorDataEditor extends Editor
{
    private TeamDataOnMap $teamData;
    private CustomTeamVectorData $customTeamVectorData;

    public function __construct(TeamGameMapData $mapData, TeamDataOnMap $teamData,CustomTeamVectorData $customTeamVectorData, Player $user, TaskScheduler $scheduler) {
        parent::__construct($mapData, $user, $scheduler);
        $this->teamData = $teamData;
        $this->customTeamVectorData = $customTeamVectorData;
    }

    /**
     * @throws \Exception
     */
    public function reloadMap(): void {
        $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
        $this->teamData = $this->mapData->getTeamData($this->teamData->getName());
        $this->customTeamVectorData = $this->teamData->getCustomVectorData($this->customTeamVectorData->getKey());

        parent::reloadMap();
    }

    /**
     * @throws \Exception
     */
    public function start(): void {
        parent::start();

        $level = Server::getInstance()->getLevelByName($this->mapData->getLevelName());
        $this->summonMarkerEntity($level, $this->customTeamVectorData->getVector3());

        $this->handler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($level): void {
            $this->summonParticle($level, $this->customTeamVectorData->getVector3());
        }), 10);
    }

    protected function summonMarkerEntity(Level $level, Vector3 $vector3): void {
        $nbt = $this->generateMarkerEntityNBT($vector3);
        if ($this->mapData instanceof TeamGameMapData) {
            $marker = new CustomTeamVectorDataMarkerEntity($this->user, $this->mapData, $this->customTeamVectorData, $level, $nbt);
            $marker->spawnTo($this->user);
        }
    }

    protected function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof CustomTeamVectorDataMarkerEntity) {
                if ($entity->getBelongMapData()->getName() === $this->mapData->getName()) $entity->kill();
            }
        }
    }
}