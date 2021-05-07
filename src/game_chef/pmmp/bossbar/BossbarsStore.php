<?php


namespace game_chef\pmmp\bossbar;


use pocketmine\Player;

class BossbarsStore
{
    /**
     * @var Bossbar[]
     */
    static private array $bossbars = [];

    static function getAll(): array {
        return self::$bossbars;
    }

    /**
     * @param Player $player
     * @return Bossbar[]
     */
    static function searchAll(Player $player): array {
        $result = [];
        foreach (self::$bossbars as $bossBar) {
            if ($bossBar->getOwner()->getName() === $player->getName()) {
                $result[] = $bossBar;
            }
        }

        return $result;
    }

    static function findById(BossbarId $id): ?Bossbar {
        foreach (self::$bossbars as $bossBar) {
            if ($bossBar->getId()->equals($id)) return $bossBar;
        }

        return null;
    }

    static function findByType(Player $player, BossbarType $type): ?Bossbar {

        foreach (self::$bossbars as $bossBar) {
            if ($bossBar->getType()->equals($type) && $bossBar->getOwner()->getName() === $player->getName()) {
                return $bossBar;
            }
        }

        return null;
    }

    static function add(Bossbar $bossBar): void {
        if (self::findById($bossBar->getId()) !== null) {
            throw new \LogicException("{$bossBar->getId()->getValue()}はすでに存在します");
        }

        if (self::findByType($bossBar->getOwner(), $bossBar->getType()) !== null) {
            throw new \LogicException("ownerおよびtypeが等しいボスバーがすでに存在します");
        }

        self::$bossbars[] = $bossBar;
    }

    static function remove(BossbarId $id): void {
        foreach (self::$bossbars as $index => $bossBar) {
            if ($bossBar->getId()->equals($id)) unset(self::$bossbars[$index]);
        }

        self::$bossbars = array_values(self::$bossbars);
    }

    static function update(Bossbar $bossBar): void {
        self::remove($bossBar->getId());
        self::add($bossBar);
    }
}