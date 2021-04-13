<?php


namespace game_chef\pmmp\scoreboard;


class Score
{
    private string $text;
    private ?int $value;
    private int $id;

    public function __construct(string $text, ?int $value = null, ?int $id = null) {
        $this->text = $text;
        $this->value = $value;
        try {
            $this->id = $id ?? random_int(0, PHP_INT_MAX);
        } catch (\Exception $exception) {
            $this->id = 0;
        }
    }

    public function getText(): string {
        return $this->text;
    }

    public function getValue(): ?int {
        return $this->value;
    }

    public function getId(): int {
        return $this->id;
    }
}