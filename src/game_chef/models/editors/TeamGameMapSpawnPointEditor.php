<?php


namespace game_chef\models\editors;


use game_chef\models\map_data\TeamDataOnMap;
use game_chef\models\map_data\TeamGameMapData;
use game_chef\pmmp\entities\TeamGameMapSpawnPointMarkerEntity;
use game_chef\repository\TeamGameMapDataRepository;
use pocketmine\level\Level;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class TeamGameMapSpawnPointEditor
{
    private Player $user;

    private TeamGameMapData $mapData;
    private TeamDataOnMap $teamData;
    private TaskScheduler $scheduler;
    private TaskHandler $handler;

    public function __construct(TeamGameMapData $teamGameMapData, TeamDataOnMap $teamDataOnMap, Player $user, TaskScheduler $scheduler) {
        $this->mapData = $teamGameMapData;
        $this->teamData = $teamDataOnMap;
        $this->user = $user;
        $this->scheduler = $scheduler;
    }

    /**
     * @throws \Exception
     */
    public function reloadMap(): void {
        $this->mapData = TeamGameMapDataRepository::loadByName($this->mapData->getName());
        $this->teamData = $this->mapData->getTeamData($this->teamData->getName());

        $this->stop();
        $this->start();
    }

    /**
     * @throws \Exception
     */
    public function start(): void {
        if ($this->user == null) {
            throw new \Exception("ユーザーがいない状態でstartすることはできません");
        }

        if (!$this->user->isOnline()) {
            throw new \Exception("ユーザーがオフラインの状態でstartすることはできません");
        }

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

    public function stop(): void {
        if ($this->handler !== null) {
            $this->handler->cancel();
        }

        $level = Server::getInstance()->getLevelByName($this->mapData->getLevelName());
        $this->deleteAllMarkerEntity($level);
    }

    private function summonParticle(Level $level, Vector3 $vector3): void {
        $center = $vector3->add(0.5, 1.3, 0.5);

        //スポーン地点を中心に直径1の円
        for ($i = 0; $i < 360; $i += 30) {
            $x = 1 * sin(deg2rad($i));
            $z = 1 * cos(deg2rad($i));

            $pos = $center->add($x, 0, $z);
            $level->addParticle(new CriticalParticle($pos));
        }

        //50m 縦に伸びるパーティクル
        for ($i = 0; $i < 50; $i += 1) {
            $pos = $center->add(0, $i, 0);
            $level->addParticle(new CriticalParticle($pos));
        }
    }

    private function summonMarkerEntity(Level $level, Vector3 $vector3): void {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $vector3->getX() + 0.5),
                new DoubleTag('', $vector3->getY() + 1.3),
                new DoubleTag('', $vector3->getZ() + 0.5)
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", 0),
                new FloatTag("", 0)
            ]),
        ]);

        $marker = new TeamGameMapSpawnPointMarkerEntity($this->user, $this->mapData, $this->teamData, $vector3, $level, $nbt);
        $marker->spawnTo($this->user);
    }

    private function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof TeamGameMapSpawnPointMarkerEntity) {
                if ($entity->getBelongMapData()->getName() === $this->mapData->getName()) $entity->kill();
            }
        }
    }
}