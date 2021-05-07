<?php


namespace game_chef\pmmp\bossbar;


use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class BossbarListener implements Listener
{
    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $bossBars = Bossbar::getBossbars($player);
        foreach ($bossBars as $bossBar) {
            $bossBar->remove();
        }
    }

    public function onTeleport(EntityTeleportEvent $event) {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $bossBars = Bossbar::getBossbars($player);
            foreach ($bossBars as $bossBar) {
                $bossBar->updateLocationInformation($player);
            }
        }
    }
}