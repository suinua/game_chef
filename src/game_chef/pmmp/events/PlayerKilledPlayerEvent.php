<?php


namespace game_chef\pmmp\events;


use pocketmine\event\Event;
use pocketmine\Player;

class PlayerKilledPlayerEvent extends Event
{
    private Player $attacker;
    private Player $killedPlayer;

    public function __construct(Player $attacker, Player $killedPlayer) {
        $this->attacker = $attacker;
        $this->killedPlayer = $killedPlayer;
    }

    public function getAttacker(): Player {
        return $this->attacker;
    }

    public function getKilledPlayer(): Player {
        return $this->killedPlayer;
    }
}