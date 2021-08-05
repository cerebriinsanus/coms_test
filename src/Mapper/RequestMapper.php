<?php

namespace App\Mapper;

use App\Dto\SearchRequestDTO;

class RequestMapper
{
    public static function SearchRequestToDTO(array $request_data) : SearchRequestDTO
    {
        $dto = new SearchRequestDTO();
        foreach ($request_data as $key => $value) {
            if ( in_array($key, ["offset", "limit"]) ) {
                $dto->$key = intval($value);
            } elseif ( in_array($key, ["date_start", "date_end"]) ) {
                if ($value) {
                    $dto->$key = new \DateTime($value);
                }
            } elseif ("ids" === $key) {
                $dto->ids = array_map('intval', explode(",", $value));
            } else {
                $dto->$key = $value;
            }
        }
        return $dto;
    }
}