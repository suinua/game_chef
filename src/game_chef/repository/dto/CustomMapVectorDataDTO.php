<?php


namespace game_chef\repository\dto;


use game_chef\models\CustomMapVectorData;
use game_chef\models\CustomMapVectorsData;

class CustomMapVectorDataDTO
{
    static function encodeVectorData(CustomMapVectorData $data): array { }

    static function decodeVectorData(array $json): CustomMapVectorData { }

    static function encodeVectorsData(CustomMapVectorsData $data): array { }

    static function decodeVectorsData(array $json): CustomMapVectorsData { }
}