<?php

namespace api\Core\Models\Role;

use api\Core\Repositories\Role\DTO\RoleDTO;

class Role
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $description,
        public bool   $active
    ) {}

    public static function fromDTO(RoleDTO $data): ?self
    {
        try {
            return new self(
                id: $data->id,
                name: $data->name,
                description: $data->description,
                active: $data->active
            );
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array<RoleDTO> $list
     * @return array<self>
     */
    public static function fromListDTO(array $list): array
    {
        try {
            return array_map(fn($item) => new self(
                id: $item->id,
                name: $item->name,
                description: $item->description,
                active: $item->active), $list);
        } catch (\Exception $e) {
            return [];
        }
    }
}