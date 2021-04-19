<?php


namespace game_chef\models\editors;


use game_chef\models\map_data\CustomMapArrayVectorData;
use game_chef\models\map_data\FFAGameMapData;
use game_chef\models\map_data\MapData;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\entities\CustomMapArrayVectorDataMarkerEntity;
use game_chef\repository\FFAGameMapDataRepository;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class CustomMapArrayVectorDataEditor extends Editor
{
    private CustomMapArrayVectorData $arrayVectorData;

    public function __construct(MapData $mapData, CustomMapArrayVectorData $arrayVectorData, Player $user, TaskScheduler $scheduler) {
        parent::__construct($mapData, $user, $scheduler);
        $this->arrayVectorData = $arrayVectorData;
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
        $this->arrayVectorData = $this->mapData->getCustomMapArrayVectorData($this->arrayVectorData->getKey());
        parent::reloadMap();
    }

    /**
     * @throws \Exception
     */
    public function start(): void {
        parent::start();

        $level = Server::getInstance()->getLevelByName($this->mapData->getLevelName());

        foreach ($this->arrayVectorData->getVector3List() as $vector3) {
            $this->summonMarkerEntity($level, $vector3);
        }

        $this->handler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($level): void {
            foreach ($this->arrayVectorData->getVector3List() as $vector3) {
                $this->summonParticle($level, $vector3);
            }
        }), 10);
    }

    protected function summonMarkerEntity(Level $level, Vector3 $vector3): void {
        $nbt = $this->generateMarkerEntityNBT($vector3);
        $marker = new CustomMapArrayVectorDataMarkerEntity($this->user->getName(), $this->mapData, $this->arrayVectorData, $vector3, $level, $nbt);
        $marker->spawnTo($this->user);
    }

    protected function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof CustomMapArrayVectorDataMarkerEntity) {
                if ($entity->getBelongMapData()->getName() === $this->mapData->getName()) $entity->kill();
            }
        }
    }
}