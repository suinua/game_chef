<?php


namespace game_chef\repository\dto;


use game_chef\models\CustomMapVectorData;
use game_chef\models\CustomMapVectorsData;

class CustomMapVectorDataDTO
{
    static function encodeVectorData(): array { }

    static function decodeVectorData(): CustomMapVectorData { }

    static function encodeVectorsData(): array { }

    static function decodeVectorsData(): CustomMapVectorsData { }
}