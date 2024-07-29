<?php

namespace api\Core\Repositories\Logistics\DTO;

use api\Core\Repositories\DTO;

class LogisticsItemDTO extends DTO
{
    public function __construct(
        public int $orderId,
        public string $cellId,
        public ?int $packageId,
        public string $barcode,
        public string $articul,
        public string $size,
        public ?int $itemId = null
    )
    {
        parent::__construct(increment: 'itemId');
    }
}