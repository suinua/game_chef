<?php


namespace game_chef\models;


class PlayerData
{
    private string $name;
    private ?GameId $belongGameId;
    private ?TeamId $belongTeamId;


    public function __construct(string $name, ?GameId $belongGameId = null, ?TeamId $belongTeamId = null) {
        $this->name = $name;
        $this->belongGameId = $belongGameId;
        $this->belongTeamId = $belongTeamId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getBelongGameId(): ?GameId {
        return $this->belongGameId;
    }

    public function getBelongTeamId(): ?TeamId {
        return $this->belongTeamId;
    }
}