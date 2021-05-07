<?php


namespace game_chef\pmmp\bossbar;

use pocketmine\Player;

class Bossbar
{
    private Player $owner;
    private BossbarId $id;
    private BossbarType $type;
    private string $title;
    private float $percentage;

    public function __construct(Player $player, BossbarType $type, string $title, float $percentage) {
        $this->owner = $player;
        $this->type = $type;
        $this->id = BossbarId::asNew();

        $this->title = $title;
        $this->percentage = $percentage;
    }

    public function send(): void {
        BossbarsStore::add($this);
        BossbarPMMPService::send($this->owner, $this);
    }

    public function remove() {
        BossbarsStore::remove($this->getId());
        BossbarPMMPService::delete($this->owner, $this->id);
    }

    public function updatePercentage(float $percentage) {
        $this->percentage = $percentage;
        BossbarsStore::update($this);
        BossbarPMMPService::updatePercentage($this->owner, $this, $percentage);
    }

    public function updateTitle(string $title) {
        $this->title = $title;
        BossbarsStore::update($this);
        BossbarPMMPService::updateTitle($this->owner, $this, $title);
    }

    public function updateLocationInformation(Player $player) {
        BossbarPMMPService::updateLocation($player, $this->id);
    }

    static function findById(BossbarId $bossBarId): ?Bossbar {
        return BossbarsStore::findById($bossBarId);
    }

    /**
     * @param Player $player
     * @return Bossbar[]
     */
    static function getBossbars(Player $player): array {
        return BossbarsStore::searchAll($player);
    }

    static function findByType(Player $player, BossbarType $type): ?Bossbar {
        return BossbarsStore::findByType($player, $type);
    }

    public function getId(): BossbarId {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getPercentage(): float {
        return $this->percentage;
    }

    public function getOwner(): Player {
        return $this->owner;
    }

    public function getType(): BossbarType {
        return $this->type;
    }
}
