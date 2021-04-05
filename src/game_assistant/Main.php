<?php


namespace game_assistant;


use game_assistant\models\PlayerData;
use game_assistant\store\PlayerDataStore;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
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
}