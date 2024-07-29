<?php
namespace api\Core\Models\Warehouse;

use api\Core\Repositories\Warehouse\DTO\WarehouseIdNameDTO;

class WarehouseIdName {
    public function __construct(
        public string $id,
        public string $name
    ) {}

    /**
     * @param array<WarehouseIdNameDTO> $list
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
