<?php

namespace api\Core\Repositories\Warehouse\DTO;

use api\Core\Repositories\DTO;

class WarehouseIdNameDTO extends DTO
{
    public function __construct(
        public string $id,
        public string $name
    ) {
        parent::__construct();
    }
}