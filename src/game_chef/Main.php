<?php


namespace game_chef;


use game_chef\models\PlayerData;
use game_chef\models\TeamGame;
use game_chef\pmmp\events\PlayerKilledPlayerEvent;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        DataFolderPath::init($this->getDataFolder());
        GameAssistant::setLogger($this->getLogger());
        GameAssistant::setScheduler($this->getScheduler());
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
        try {
            PlayerDataStore::delete($event->getPlayer()->getName());
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            $event->setCancelled();
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
        if ($cause instanceof EntityDamageByEntityEvent) {
            $attacker = $cause->getDamager();
            if ($attacker instanceof Player) {
                (new PlayerKilledPlayerEvent($attacker, $killedPlayer))->call();
            }
        }
    }
}