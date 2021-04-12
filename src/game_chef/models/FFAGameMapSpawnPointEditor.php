<?php


namespace game_chef\models;

use game_chef\models\FFAGameMap;
use game_chef\pmmp\entities\FFAGameMapSpawnPointMarkerEntity;
use game_chef\pmmp\form\ffa_game_map_forms\FFAGameMapDetailForm;
use game_chef\pmmp\hotbar_menu\DeleteFFASpawnPointHotbarMenu;
use game_chef\pmmp\hotbar_menu\FFAGameSpawnPointsHotbarMenu;
use game_chef\repository\FFAGameMapRepository;
use pocketmine\block\Ice;
use pocketmine\level\Level;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\DestroyBlockParticle;
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

//TODO : これが起動してるときに、対象のマップが削除されたりした場合の処理
class FFAGameMapSpawnPointEditor
{
    private Player $user;

    private FFAGameMap $map;
    private TaskScheduler $scheduler;
    private TaskHandler $handler;

    public function __construct(FFAGameMap $ffaGameMap, Player $user, TaskScheduler $scheduler) {
        $this->map = $ffaGameMap;
        $this->user = $user;
        $this->scheduler = $scheduler;
    }

    /**
     * @param FFAGameMap $map
     * @throws \Exception
     */
    public function reloadMap(): void {
        if ($this->handler !== null) {
            $this->handler->cancel();
        }
        $this->map = FFAGameMapRepository::loadByName($this->map->getName());
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

        $level = Server::getInstance()->getLevelByName($this->map->getLevelName());

        foreach ($this->map->getSpawnPoints() as $spawnPoint) {
            $this->summonMarkerEntity($level, $spawnPoint);
        }

        $this->handler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($level): void {
            foreach ($this->map->getSpawnPoints() as $spawnPoint) {
                $this->summonParticle($level, $spawnPoint);
            }
        }), 20);

        $menu = new FFAGameSpawnPointsHotbarMenu($this->user,$this->map);
        $menu->send();
    }

    public function stop(): void {
        if ($this->handler !== null) {
            $this->handler->cancel();
        }

        $level = Server::getInstance()->getLevelByName($this->map->getLevelName());
        $this->deleteAllMarkerEntity($level);

        if ($this->user !== null) {
            if ($this->user->isOnline()) {
                $this->user->sendForm(new FFAGameMapDetailForm($this->map));
            }
        }
    }

    private function summonParticle(Level $level, Vector3 $vector3): void {
        $center = $vector3;

        //スポーン地点を中心に直径1の円
        for ($i = 0; $i < 360; $i += 30) {
            $x = 0.5 * sin(deg2rad($i));
            $z = 0.5 * cos(deg2rad($i));

            $pos = $center->add($x, 0.3, $z);
            $level->addParticle(new CriticalParticle($pos));
        }

        //100m 縦に伸びるパーティクル
        for ($i = 0; $i < 100; $i += 1) {
            $pos = $vector3->add(0, $i, 0);
            $level->addParticle(new CriticalParticle($pos));
        }
    }

    private function summonMarkerEntity(Level $level, Vector3 $vector3): void {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $vector3->getX()),
                new DoubleTag('', $vector3->getY() + 1),
                new DoubleTag('', $vector3->getZ())
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

        $marker = new FFAGameMapSpawnPointMarkerEntity($this->user, $this->map, $vector3, $level, $nbt);
        $marker->spawnTo($this->user);
    }

    private function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof FFAGameMapSpawnPointMarkerEntity) {
                if ($entity->getBelongMap()->getName() === $this->map->getName()) $entity->kill();
            }
        }
    }
}