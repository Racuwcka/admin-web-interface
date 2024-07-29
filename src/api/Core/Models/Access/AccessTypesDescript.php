<?php

namespace api\Core\Models\Access;

use api\Core\Repositories\Access\DTO\AccessTypeDescriptDTO;

class AccessTypesDescript
{
    public function __construct(
        public string $type,
        public string $descript
    ) {}

    /**
     * @param array<AccessTypeDescriptDTO> $list
     * @return array<self>
     */
    public static function fromListDTO(array $list): array
    {
        try {
            return array_map(fn($item) => new self(
                type: $item->typeId,
                descript: $item->descript), $list);
        } catch (\Exception $e) {
            return [];
        }
    }
}