<?php


namespace game_chef;


use game_chef\models\PlayerData;
use game_chef\models\TeamGame;
use game_chef\pmmp\entities\NPCBase;
use game_chef\pmmp\events\PlayerKilledPlayerEvent;
use game_chef\store\FFAGameMapSpawnPointEditorStore;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        TaskSchedulerStorage::init($this->getScheduler());
        DataFolderPath::init($this->getDataFolder(), $this->getFile() . "resources/");
        GameChef::setLogger($this->getLogger());
        GameChef::setScheduler($this->getScheduler());
    }

    public function onJoin(PlayerJoinEvent $event) {
        try {
            PlayerDataStore::add(new PlayerData($event->getPlayer()->getName()));
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $event->setCancelled();
        }
    }

    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();

        try {
            PlayerDataStore::delete($player->getName());
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $event->setCancelled();
        }

        if (FFAGameMapSpawnPointEditorStore::isExist($player->getName())) {
            try {
                FFAGameMapSpawnPointEditorStore::delete($player->getName());
            } catch (\Exception $e) {
                $this->getLogger()->error($e);
            }
        }
    }

    public function onPlayerDamagedByPlayer(EntityDamageByEntityEvent $event) {
        $damagedPlayer = $event->getEntity();
        $attackingPlayer = $event->getDamager();

        if ($damagedPlayer instanceof Player and $attackingPlayer instanceof Player) {
            try {
                $damagedPlayerData = PlayerDataStore::getByName($damagedPlayer->getName());
                $attackingPlayerData = PlayerDataStore::getByName($attackingPlayer->getName());

                if ($damagedPlayerData->getBelongGameId() === null or $attackingPlayerData->getBelongGameId() === null) return;
                if (!$damagedPlayerData->getBelongGameId()->equals($attackingPlayerData->getBelongGameId())) return;

                $game = GamesStore::getById($damagedPlayerData->getBelongGameId());
                if ($game instanceof TeamGame) {
                    if (!$game->getFriendlyFire()) {
                        $event->setCancelled();
                        return;
                    }
                }
            } catch (\Exception $exception) {
                //イベントはスルー
                $this->getLogger()->error($exception);
            }
        }
    }

    public function onPlayerKilledPlayer(PlayerDeathEvent $event) {
        $killedPlayer = $event->getPlayer();
        $cause = $killedPlayer->getLastDamageCause();
        if (!$cause instanceof EntityDamageByEntityEvent) return;

        $attacker = $cause->getDamager();
        if (!$attacker instanceof Player) return;

        try {
            $attackerData = PlayerDataStore::getByName($attacker->getName());
            $killedPlayerData = PlayerDataStore::getByName($killedPlayer->getName());
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            return;
        }

        if ($attackerData->getBelongGameId() === null || $killedPlayerData->getBelongGameId() === null) return;
        if ($attackerData->getBelongGameId()->equals($killedPlayerData->getBelongGameId())) {
            (new PlayerKilledPlayerEvent($attacker, $killedPlayer))->call();
        }
    }

    public function onDamagedNPC(EntityDamageEvent $event) {

        $npc = $event->getEntity();
        if (!$npc instanceof NPCBase) return;
        if (!$event instanceof EntityDamageByEntityEvent) {
            $event->setCancelled();
            return;
        }

        $player = $event->getDamager();
        if ($player instanceof Player) {
            $npc->onTap($player);
        } else {
            $event->setCancelled();
        }
    }
}