<?php

namespace api\Core\Models\Queue;

use api\Core\Repositories\Queue\DTO\QueueDTO;

class Queue
{
    public function __construct(
        public int $id,
        public string $status,
        public int    $count
    ) {}

    /**
     * @param array<QueueDTO> $list
     * @return array<self>
     */
    public static function fromListDTO(array $list): array
    {
        try {
            return array_map(fn($item) => new self(
                id: $item->id,
                status: $item->status,
                count: $item->count), $list);
        } catch (\Exception $e) {
            return [];
        }
    }
}