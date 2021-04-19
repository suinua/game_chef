<?php


namespace game_chef;


use game_chef\api\GameChef;
use game_chef\models\PlayerData;
use game_chef\models\TeamGame;
use game_chef\pmmp\bossbar\BossbarListener;
use game_chef\pmmp\entities\NPCBase;
use game_chef\pmmp\events\PlayerKilledPlayerEvent;
use game_chef\pmmp\form\MainMapForm;
use game_chef\pmmp\hotbar_menu\HotbarMenuItem;
use game_chef\services\GameService;
use game_chef\store\EditorsStore;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
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

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new BossbarListener(), $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$sender instanceof Player) return false;
        if ($label === "map") {
            $sender->sendForm(new MainMapForm());
            return true;
        }
        return false;
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
            if (PlayerDataStore::getByName($player->getName())->getBelongGameId() !== null) GameService::quit($player);
            PlayerDataStore::delete($player->getName());
        } catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage());
            return;
        }

        if (EditorsStore::isExist($player->getName())) {
            try {
                EditorsStore::delete($player->getName());
            } catch (\Exception $e) {
                $this->getLogger()->error($e->getMessage());
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
            try {
                $game = GamesStore::getById($attackerData->getBelongGameId());
            } catch (\Exception $e) {
                $this->getLogger()->error($e->getMessage());
                return;
            }

            $isFriendlyFire = $attackerData->getBelongTeamId()->equals($killedPlayerData->getBelongTeamId());

            (new PlayerKilledPlayerEvent($game->getId(), $game->getType(), $attacker, $killedPlayer, $isFriendlyFire))->call();
        }
    }

    public function onDamagedNPC(EntityDamageEvent $event) {

        $npc = $event->getEntity();
        if (!$npc instanceof NPCBase) return;
        $event->setCancelled();
        if (!$event instanceof EntityDamageByEntityEvent) return;

        $player = $event->getDamager();
        if ($player instanceof Player) $npc->onTap($player);
    }

    public function onBeakBlock(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item instanceof HotbarMenuItem) {
            $item->onTapBlock($player, $event->getBlock());
            $event->setCancelled();
        }
    }

    public function onTapBlock(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item instanceof HotbarMenuItem) {
            if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                $item->onTapBlock($player, $event->getBlock());
            }
        }
    }
}