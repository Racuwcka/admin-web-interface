<?php

namespace api\Core\Models\Region;

use api\Core\Repositories\Region\DTO\RegionDTO;

class Region
{
    public function __construct(
        public string $id,
        public string $name
    ) {}

    /**
     * @param array<RegionDTO> $list
     * @return array<self>
     */
    public static function fromListDTO(array $list): array
    {
        try {
            return array_map(fn($item) => new self(
                id: $item->id,
                name: $item->name), $list);
        } catch (\Exception $e) {
            return [];
        }
    }
}