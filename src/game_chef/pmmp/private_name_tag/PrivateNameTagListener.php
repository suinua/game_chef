<?php


namespace game_chef\pmmp\private_name_tag;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class PrivateNameTagListener implements Listener
{
    public function onQuit(PlayerQuitEvent $event) {
        PrivateNameTag::remove($event->getPlayer());
    }

    public function onReceiveDamaged(EntityDamageByEntityEvent $event) {
        $victim = $event->getEntity();
        if ($victim instanceof PrivateNameTag) $event->setCancelled();
    }

    public function onDead(PlayerDeathEvent $event) {
        PrivateNameTag::remove($event->getPlayer());
    }

    public function onTeleport(EntityTeleportEvent $event) {
        $player = $event->getEntity();
        $from = $event->getFrom();
        $to = $event->getTo();
        if ($player instanceof Player) {
            //レベルの移動なしだったら
            if ($to->getLevel() === null) return;
            if ($from->getLevelNonNull()->getId() === $to->getLevelNonNull()->getId()) return;
            PrivateNameTag::remove($player);
        }
    }
}