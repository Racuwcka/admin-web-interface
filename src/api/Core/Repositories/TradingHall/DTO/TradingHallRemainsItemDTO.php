<?php

namespace api\Core\Repositories\TradingHall\DTO;

use api\Core\Repositories\DTO;

class TradingHallRemainsItemDTO extends DTO
{
    /**
     * @param array<string> $sizes
     * @param string $articul
     * @param string $category
     */
    public function __construct(
        public array $sizes,
        public string $articul,
        public string $category
    ) {
        parent::__construct();
    }
}