<?php

namespace api\Core\Repositories\TransitStorage\DTO;

use api\Core\Repositories\DTO;

class TransitStorageDTO extends DTO
{
    public function __construct(
        public string $orderId,
        public string $type,
        public string $warehouse,
        public string $userId,
        public string $userName,
        public float $date,
        public ?int $id = null
    ) {
        parent::__construct(increment: 'id');
    }

}