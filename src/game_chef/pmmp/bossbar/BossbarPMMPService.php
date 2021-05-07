<?php


namespace game_chef\pmmp\bossbar;


use pocketmine\entity\Attribute;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\Player;

class BossbarPMMPService
{
    static function send(Player $player, Bossbar $bossBar): void {

        $addActorPacket = new AddActorPacket();
        $addActorPacket->entityRuntimeId = $bossBar->getId()->getValue();
        $addActorPacket->type = "minecraft:slime";
        $addActorPacket->position = $player->getPosition();
        $addActorPacket->metadata = [
            Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
            Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 0 ^ 1 << Entity::DATA_FLAG_SILENT ^ 1 << Entity::DATA_FLAG_INVISIBLE ^ 1 << Entity::DATA_FLAG_NO_AI],
            Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0],
            Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $bossBar->getTitle()],
            Entity::DATA_BOUNDING_BOX_WIDTH => [Entity::DATA_TYPE_FLOAT, 0],
            Entity::DATA_BOUNDING_BOX_HEIGHT => [Entity::DATA_TYPE_FLOAT, 0]
        ];
        $player->dataPacket($addActorPacket);

        $bossEventPacket = new BossEventPacket();
        $bossEventPacket->bossEid = $bossBar->getId()->getValue();
        $bossEventPacket->eventType = BossEventPacket::TYPE_SHOW;
        $bossEventPacket->title = $bossBar->getTitle();
        $bossEventPacket->healthPercent = $bossBar->getPercentage();
        $bossEventPacket->unknownShort = 0;
        $bossEventPacket->color = 0;
        $bossEventPacket->overlay = 0;
        $bossEventPacket->playerEid = 0;

        $player->dataPacket($bossEventPacket);
    }

    static function delete(Player $player, BossbarId $id): void {
        $rpk = new RemoveActorPacket();
        $rpk->entityUniqueId = $id->getValue();

        $player->dataPacket($rpk);
    }

    static function updateLocation(Player $player, BossbarId $id): void {
        $moveActorPacket = new MoveActorAbsolutePacket();
        $moveActorPacket->entityRuntimeId = $id->getValue();
        $moveActorPacket->flags |= MoveActorAbsolutePacket::FLAG_TELEPORT;
        $moveActorPacket->position = $player->getPosition();
        $moveActorPacket->xRot = 0;
        $moveActorPacket->yRot = 0;
        $moveActorPacket->zRot = 0;

        $player->dataPacket($moveActorPacket);
    }

    static function updatePercentage(Player $player, Bossbar $bossBar, float $percentage): void {
        $percentage = $percentage <= 0 ? 0.001 : $percentage;

        $attribute = Attribute::getAttribute(Attribute::HEALTH);
        $attribute->setMaxValue(1000);
        $attribute->setValue(1000 * $percentage);
        $upk = new UpdateAttributesPacket();
        $upk->entries = [$attribute];
        $upk->entityRuntimeId = $bossBar->getId()->getValue();
        $player->dataPacket($upk);

        $bossEventPacket = new BossEventPacket();
        $bossEventPacket->bossEid = $bossBar->getId()->getValue();
        $bossEventPacket->eventType = BossEventPacket::TYPE_HEALTH_PERCENT;
        $bossEventPacket->healthPercent = $percentage;
        $bossEventPacket->unknownShort = 0;
        $bossEventPacket->color = 0;
        $bossEventPacket->overlay = 0;
        $bossEventPacket->playerEid = 0;
        $player->dataPacket($bossEventPacket);
    }

    static function updateTitle(Player $player, Bossbar $bossBar, string $title): void {

        $setActorPacket = new SetActorDataPacket();
        $setActorPacket->metadata = [Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title]];
        $setActorPacket->entityRuntimeId = $bossBar->getId()->getValue();

        $player->dataPacket($setActorPacket);

        $bossEventPacket = new BossEventPacket();
        $bossEventPacket->bossEid = $bossBar->getId()->getValue();
        $bossEventPacket->eventType = BossEventPacket::TYPE_TITLE;
        $bossEventPacket->title = $title;
        $bossEventPacket->unknownShort = 0;
        $bossEventPacket->color = 0;
        $bossEventPacket->overlay = 0;
        $bossEventPacket->playerEid = 0;

        $player->dataPacket($bossEventPacket);
    }
}