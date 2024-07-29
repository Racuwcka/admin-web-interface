<?php

namespace api\Core\Models\WorkArea;

use api\Core\Repositories\WorkArea\DTO\WorkAreaDTO;
use api\Services\WarehouseService;

class WorkArea {
    public function __construct(
        public int $id,
        public string $name,
        public string $warehouse,
        public string $warehouseName
    ) {}

    public static function fromDTO(WorkAreaDTO $data): ?self
    {
        try {
            return new self(
                id: $data->id,
                name: $data->name,
                warehouse: $data->warehouse,
                warehouseName: WarehouseService::getName($data->warehouse)
            );
        }
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array<WorkAreaDTO> $list
     * @return array<self>
     */
    public static function fromListDTO(array $list): array
    {
        try {
            return array_map(fn($item) => new self(
                id: $item->id,
                name: $item->name,
                warehouse: $item->warehouse,
                warehouseName: WarehouseService::getName($item->warehouse)), $list);
        } catch (\Exception $e) {
            return [];
        }
    }
}
