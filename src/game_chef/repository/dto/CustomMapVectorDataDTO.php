<?php


namespace game_chef\repository\dto;


use game_chef\models\CustomMapVectorData;
use game_chef\models\CustomMapArrayVectorData;

class CustomMapVectorDataDTO
{
    static function encodeVectorData(CustomMapVectorData $data): array { }

    static function decodeVectorData(array $json): CustomMapVectorData { }

    static function encodeArrayVectorsData(CustomMapArrayVectorData $data): array { }

    static function decodeArrayVectorData(array $json): CustomMapArrayVectorData { }
}