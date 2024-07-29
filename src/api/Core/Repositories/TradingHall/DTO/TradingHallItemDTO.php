<?php

namespace api\Core\Repositories\TradingHall\DTO;

use api\Core\Repositories\DTO;

class TradingHallItemDTO extends DTO
{
    public function __construct(
        public string $hash,
        public string $warehouse,
        public string $day,
        public string $articul,
        public string $size,
        public ?int $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}