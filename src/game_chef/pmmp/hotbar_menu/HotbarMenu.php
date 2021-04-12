<?php


namespace game_chef\pmmp\hotbar_menu;


use pocketmine\Player;

class HotbarMenu
{
    protected Player $player;
    protected array $menuItems;

    public function __construct(Player $player, array $menuItems) {
        $this->player = $player;
        $this->menuItems = $menuItems;
    }

    public function send(): void {
        if ($this->player->isOnline()) {
            $this->player->getInventory()->setContents($this->menuItems);
        }
    }

    public function close(): void {
        if ($this->player->isOnline()) {
            $this->player->getInventory()->setContents([]);
        }
    }
}