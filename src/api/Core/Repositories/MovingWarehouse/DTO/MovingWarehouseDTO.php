<?php

namespace api\Core\Repositories\MovingWarehouse\DTO;

use api\Core\Repositories\DTO;

class MovingWarehouseDTO extends DTO
{
    public function __construct(
        public string $id,
        public string $name,
        public int $supportPackage,
        public int $accept
    ) {
        parent::__construct();
    }
}