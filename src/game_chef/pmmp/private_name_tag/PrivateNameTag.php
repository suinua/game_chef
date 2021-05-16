<?php


namespace game_chef\pmmp\private_name_tag;


use game_chef\api\GameChef;
use game_chef\models\GameId;
use game_chef\models\PlayerData;
use game_chef\models\Team;
use game_chef\pmmp\entities\NPCBase;
use game_chef\store\GamesStore;
use game_chef\store\PlayerDataStore;
use PHPUnit\Util\Color;
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

    private static function setup(Player $player, string $text, array $viewers): self {
        $entity = new self($player->getLevel(), self::createBaseNBT($player->asVector3()));
        $entity->setOwningEntity($player);
        //$entity->namedtag->setIntArray("viewer_names", array_map(fn(Player $viewer) => $viewer->getName(), $viewers));
        $entity->setNameTag($text);
        $entity->setNameTagAlwaysVisible(true);

        foreach ($viewers as $viewer) {
            $entity->spawnTo($viewer);
        }

        $setEntity = new SetActorLinkPacket();
        $setEntity->link = new EntityLink($player->getId(), $entity->getId(), EntityLink::TYPE_RIDER, true, true);

        $player->setGenericFlag(Entity::DATA_FLAG_RIDING, true);
        Server::getInstance()->broadcastPacket($viewers, $setEntity);

        return $entity;
    }

    public static function updateText(PrivateNameTag $privateNameTag, string $text): void {
        $privateNameTag->setNameTag($text);
    }


    public static function updateViewers(PrivateNameTag $privateNameTag, array $viewers): ?self {
        $owner = $privateNameTag->getOwningEntity();
        $privateNameTag->setInvisible(true);
        $privateNameTag->kill();

        if ($owner instanceof Player) {
            return self::setup($owner, $privateNameTag->getNameTag(), $viewers);
        } else {
            return null;
        }
    }

    public static function set(Player $player, string $text, array $viewers): void {
        $entity = self::find($player);

        if ($entity === null) {
            self::setup($player, $text, $viewers);
            return;
        }

        $entity = self::updateViewers($entity, $viewers);
        if ($entity->getNameTag() !== $text) {
            self::updateText($entity, $text);
        }
        return;
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

    public static function find(Player $player): ?self {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof PrivateNameTag) {
                if ($entity->getOwningEntityId() === $player->getId()) {
                    return $entity;
                }
            }
        }

        return null;
    }

    public static function setTeamNameTag(GameId $gameId, \Closure $closure) {
        $game = GamesStore::getById($gameId);
        foreach ($game->getTeams() as $team) {
            $teamPlayers = array_map(fn(PlayerData $playerData) => Server::getInstance()->getPlayer($playerData->getName()), GameChef::getTeamPlayerDataList($team->getId()));
            foreach ($teamPlayers as $teamPlayer) {
                PrivateNameTag::set($teamPlayer, $closure($team, $teamPlayer), $teamPlayers);
            }
        }
    }
}