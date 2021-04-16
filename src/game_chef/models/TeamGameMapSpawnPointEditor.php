<?php


namespace game_chef\models;


use game_chef\pmmp\entities\TeamGameMapSpawnPointMarkerEntity;
use game_chef\pmmp\form\team_game_map_forms\TeamDataDetailForm;
use game_chef\pmmp\hotbar_menu\TeamGameSpawnPointsHotbarMenu;
use game_chef\repository\TeamGameMapRepository;
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

    private TeamGameMap $map;
    private TeamDataOnMap $teamData;
    private TaskScheduler $scheduler;
    private TaskHandler $handler;

    public function __construct(TeamGameMap $teamGameMap, TeamDataOnMap $teamDataOnMap, Player $user, TaskScheduler $scheduler) {
        $this->map = $teamGameMap;
        $this->teamData = $teamDataOnMap;
        $this->user = $user;
        $this->scheduler = $scheduler;
    }

    /**
     * @throws \Exception
     */
    public function reloadMap(): void {
        if ($this->handler !== null) {
            $this->handler->cancel();
        }
        $this->map = TeamGameMapRepository::loadByName($this->map->getName());
        $this->teamData = $this->map->getTeamDataOnMapByName($this->teamData->getTeamName());

        $level = Server::getInstance()->getLevelByName($this->map->getLevelName());
        $this->reloadMarkerEntity($level);
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

        foreach ($this->teamData->getSpawnPoints() as $spawnPoint) {
            $this->summonMarkerEntity($level, $spawnPoint);
        }

        $this->handler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $currentTick) use ($level): void {
            foreach ($this->teamData->getSpawnPoints() as $spawnPoint) {
                $this->summonParticle($level, $spawnPoint);
            }
        }), 10);

        $menu = new TeamGameSpawnPointsHotbarMenu($this->user, $this->map, $this->teamData);
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
                $this->user->sendForm(new TeamDataDetailForm($this->map, $this->teamData));
            }
        }
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

        $marker = new TeamGameMapSpawnPointMarkerEntity($this->user, $this->map, $this->teamData, $vector3, $level, $nbt);
        $marker->spawnTo($this->user);
    }

    private function deleteAllMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof TeamGameMapSpawnPointMarkerEntity) {
                if ($entity->getBelongMap()->getName() === $this->map->getName()) $entity->kill();
            }
        }
    }

    private function reloadMarkerEntity(Level $level): void {
        foreach ($level->getEntities() as $entity) {
            if (!$entity instanceof TeamGameMapSpawnPointMarkerEntity) continue;
            if ($entity->getBelongMap()->getName() !== $this->map->getName()) continue;

            $isExistOnMapData = false;
            foreach ($this->teamData->getSpawnPoints() as $spawnPoint) {
                if ($entity->getMapSpawnPoint()->equals($spawnPoint)) {
                    $isExistOnMapData = true;
                }
            }
            if (!$isExistOnMapData) $entity->kill();
        }
    }
}