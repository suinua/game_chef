<?php


namespace game_chef\pmmp\bossbar;



use pocketmine\entity\Entity;

class BossbarId
{
    private string $value;

    public function __construct(int $value) {
        $this->value = $value;
    }

    static function asNew(): BossbarId {
        return new BossbarId(Entity::$entityCount++);
    }

    public function equals(?BossbarId $id): bool {
        if ($id === null)
            return false;

        return $this->value === $id->value;
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }
}