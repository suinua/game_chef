<?php


namespace game_chef\pmmp\hotbar_menu;


use Closure;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\Player;

class HotbarMenuItem extends Item
{
    private ?Closure $onTapBlock;
    private ?Closure $onTap;

    public function __construct(int $itemId, int $meta, string $name, ?Closure $onTap = null, ?Closure $onTapBlock = null) {
        $this->onTapBlock = $onTapBlock;
        $this->onTap = $onTap;
        parent::__construct($itemId, $meta, $name);
    }

    public function onTapBlock(Player $player, Block $block) {
        if ($this->onTapBlock !== null) {
            ($this->onTapBlock)($player, $block);
        } else {
            $this->onTap($player);
        }
    }

    public function onTap(Player $player) {
        if ($this->onTap === null) return;
        ($this->onTap)($player);
    }
}