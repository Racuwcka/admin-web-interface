<?php

namespace api\Core\Repositories\InventoryCell\DTO;

use api\Core\Repositories\DTO;

class InventoryCellActionDataDTO extends DTO
{
    public function __construct(
        public string    $cellId,
        public string    $cellName,
        public string $barcode,
        public string    $articul,
        public string    $size,
        public int    $qty
    ) {
        parent::__construct();
    }
}