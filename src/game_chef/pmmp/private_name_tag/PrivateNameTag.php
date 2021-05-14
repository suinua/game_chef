<?php


namespace game_chef\pmmp\private_name_tag;


use game_chef\pmmp\entities\NPCBase;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\Server;

class PrivateNameTag extends NPCBase
{
    const NAME = "FFAGameMapSpawnPointMarkerEntity";
    public string $skinName = "empty";
    protected string $geometryId = "geometry.empty";
    protected string $geometryName = "empty.geo.json";

    private static function setup(Player $player, string $text, array $viewers): void {
        $entity = new self($player->getLevel(), self::createBaseNBT($player->asVector3()));
        $entity->setOwningEntity($player);
        $entity->namedtag->setIntArray("viewer_ids", array_map(fn(Player $viewer) => $viewer->getId(), $viewers));
        $entity->setNameTag($text);
        $entity->setNameTagAlwaysVisible(true);

        foreach ($viewers as $viewer) {
            $entity->spawnTo($viewer);
        }

        $setEntity = new SetActorLinkPacket();
        $setEntity->link = new EntityLink($player->getId(), $entity->getId(), EntityLink::TYPE_RIDER, true, true);

        $player->setGenericFlag(Entity::DATA_FLAG_RIDING, true);
        Server::getInstance()->broadcastPacket($viewers, $setEntity);
    }

    private static function updateText(PrivateNameTag $privateNameTag, string $text): void {
        $privateNameTag->setNameTag($text);
    }


    private static function updateViewers(PrivateNameTag $privateNameTag, array $viewers): void {
        $privateNameTag->kill();
        $owner = $privateNameTag->getOwningEntity();
        if ($owner instanceof Player) {
            self::set($owner, $privateNameTag->getNameTag(), $viewers);
        }
    }

    public static function set(Player $player, string $text, array $viewers): void {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof PrivateNameTag) {
                if ($entity->getOwningEntityId() === $player->getId()) {
                    $viewerIds = array_map(fn(Player $viewer) => $viewer->getId(), $viewers);
                    if ($entity->namedtag->getIntArray("viewers") !== $viewerIds) {
                        self::updateViewers($entity, $viewers);
                    }
                    if ($entity->getNameTag() !== $text) {
                        self::updateText($entity, $text);
                    }
                    return;
                }
            }
        }

        self::setup($player, $text, $viewers);
    }

    public static function remove(Player $player) {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof PrivateNameTag) {
                if ($entity->getOwningEntityId() === $player->getId()) {
                    $entity->kill();
                }
            }
        }
    }
}