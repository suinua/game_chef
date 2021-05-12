<?php


namespace game_chef\models\editors;


use game_chef\models\map_data\MapData;
use pocketmine\level\Level;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

abstract class Editor
{
    protected Player $user;

    protected MapData $mapData;
    protected TaskScheduler $scheduler;
    protected TaskHandler $handler;


    public function __construct(MapData $mapData, Player $user, TaskScheduler $scheduler) {
        $this->mapData = $mapData;
        $this->user = $user;
        $this->scheduler = $scheduler;
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
    }

    public function stop(): void {
        if ($this->handler !== null) {
            $this->handler->cancel();
        }

        $level = Server::getInstance()->getLevelByName($this->mapData->getLevelName());
        $this->deleteAllMarkerEntity($level);
    }

    /**
     * @throws \Exception
     */
    public function reloadMap(): void {
        $this->stop();
        $this->start();
    }

    abstract protected function summonMarkerEntity(Level $level, Vector3 $vector3): void;

    abstract protected function deleteAllMarkerEntity(Level $level): void;

    protected function summonParticle(Level $level, Vector3 $vector3): void {
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
}