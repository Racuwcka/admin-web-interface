<?php

namespace api\Core\Repositories\TradingHall\DTO;

use api\Core\Repositories\DTO;

class TradingHallRemainsDTO extends DTO
{
    public function __construct(
        public string $warehouse,
        public string $day,
        public string $data,
        public ?int $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}