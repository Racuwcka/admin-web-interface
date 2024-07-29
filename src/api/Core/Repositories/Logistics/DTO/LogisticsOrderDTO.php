<?php

namespace api\Core\Repositories\Logistics\DTO;

use api\Core\Repositories\DTO;

class LogisticsOrderDTO extends DTO
{
    public function __construct(
        public int $orderId,
        public string $cellId,
        public string $cellName,
        public string $warehouse,
        public ?int $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}