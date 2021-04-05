<?php


namespace game_assistant\models;


class Map
{
    protected string $name;
    protected string $levelName;
    /**
     * @var GameType[]
     */
    protected array $adaptedGameTypes;

    public function __construct(string $name, string $levelName, array $adaptedGameTypes) {
        $this->name = $name;
        $this->levelName = $levelName;
        $this->adaptedGameTypes = $adaptedGameTypes;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLevelName(): string {
        return $this->levelName;
    }

    /**
     * @return GameType[]
     */
    public function getAdaptedGameTypes(): array {
        return $this->adaptedGameTypes;
    }

    public function isAdaptedGameType(GameType $gameType): bool {
        foreach ($this->adaptedGameTypes as $adaptedGameType) {
            if ($gameType->equals($adaptedGameType)) {
                return true;
            }
        }

        return false;
    }
}