<?php


namespace game_chef\pmmp\hotbar_menu;


use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\Player;

class HotbarMenuItem extends Item
{
    private \Closure $onTapBlock;

    public function __construct(int $itemId, string $name, \Closure $onTapBlock) {
        parent::__construct($itemId, 0, $name);
    }

    public function onTapBlock(Player $player, Block $block) {
        ($this->onTapBlock)($player, $block);
    }
}