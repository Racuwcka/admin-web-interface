<?php

namespace api\Core\Repositories\InventoryCell\DTO;

use api\Core\Repositories\DTO;

class InventoryCellShelfActionCountDTO extends DTO
{
    public function __construct(
        public int $shelf,
        public int $qty
    ) {
        parent::__construct();
    }
}