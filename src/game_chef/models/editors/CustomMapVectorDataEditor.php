<?php


namespace game_chef\models\editors;


use game_chef\models\map_data\CustomMapVectorData;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\entities\CustomMapVectorDataMarkerEntity;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class CustomMapVectorDataEditor extends Editor
{
    private CustomMapVectorData $customMapVectorData;

    public function __construct(MapData $mapData, CustomMapVectorData $customMapVectorData, Player $user, TaskScheduler $scheduler) {
        parent::__construct($mapData, $user, $scheduler);
        $this->customMapVectorData = $customMapVectorData;
    }

    /**
     * @throws \Exception
     */
    public function reloadMap(): void {
        if ($this->mapData instanceof TeamGameMapData) {
            $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
        } else if ($this->mapData instanceof FFAGameMapData) {
            $this->mapData = FFAGameMapDataRepository::loadByName($this->mapData->getName());
        }
        $this->customMapVectorData = $this->mapData->getCustomMapVectorData($this->customMapVectorData->getKey());

        parent::reloadMap();
    }

    /**
     * @throws \Exception
     */
    public function start(): void {
        parent::start();

        $level = Server::getInstance()->getLevelByName($this->mapData->getLevelName());
        $this->summonMarkerEntity($level, $this->customMapVectorData->getVector3());

        $this->handler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($level): void {
            $this->summonParticle($level, $this->customMapVectorData->getVector3());
        }), 10);
    }

    protected function summonMarkerEntity(Level $level, Vector3 $vector3): void {
        $marker = CustomMapVectorDataMarkerEntity::create($this->user, $this->customMapVectorData);
        $marker->spawnTo($this->user);
    }

    protected function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof CustomMapVectorDataMarkerEntity) {
                if ($entity->getBelongMapName() === $this->mapData->getName()) $entity->kill();
            }
        }
    }
}