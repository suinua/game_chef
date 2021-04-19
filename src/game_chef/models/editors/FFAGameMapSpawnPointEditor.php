<?php


namespace game_chef\models\editors;

use game_chef\models\map_data\FFAGameMapData;
use game_chef\pmmp\entities\FFAGameMapSpawnPointMarkerEntity;
use game_chef\repository\FFAGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

//TODO : これが起動してるときに、対象のマップが削除されたりした場合の処理
class FFAGameMapSpawnPointEditor extends Editor
{
    public function __construct(FFAGameMapData $mapData, Player $user, TaskScheduler $scheduler) {
        parent::__construct($mapData, $user, $scheduler);
    }


    /**
     * @throws \Exception
     */
    public function reloadMap(): void {
        $this->mapData = FFAGameMapDataRepository::loadByName($this->mapData->getName());
        parent::reloadMap();
    }

    /**
     * @throws \Exception
     */
    public function start(): void {
        parent::start();

        $level = Server::getInstance()->getLevelByName($this->mapData->getLevelName());
        if (!$this->mapData instanceof FFAGameMapData) {
            throw new \Exception("FFAGameMapSpawnPointEditorにFFAGameMapData以外のマップデータを入れた状態でstartすることはできません");
        }

        foreach ($this->mapData->getSpawnPoints() as $spawnPoint) {
            $this->summonMarkerEntity($level, $spawnPoint);
        }

        $this->handler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($level): void {
            foreach ($this->mapData->getSpawnPoints() as $spawnPoint) {
                $this->summonParticle($level, $spawnPoint);
            }
        }), 10);
    }
    protected function summonMarkerEntity(Level $level, Vector3 $vector3): void {
        $nbt = $this->generateMarkerEntityNBT($vector3);
        if ($this->mapData instanceof FFAGameMapData) {
            $marker = new FFAGameMapSpawnPointMarkerEntity($this->user, $this->mapData, $vector3, $level, $nbt);
            $marker->spawnTo($this->user);
        }
    }

    protected function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof FFAGameMapSpawnPointMarkerEntity) {
                if ($entity->getBelongMapData()->getName() === $this->mapData->getName()) $entity->kill();
            }
        }
    }
}