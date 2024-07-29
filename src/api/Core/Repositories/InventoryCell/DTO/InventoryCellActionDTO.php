<?php

namespace api\Core\Repositories\InventoryCell\DTO;

use api\Core\Repositories\DTO;

class InventoryCellActionDTO extends DTO
{
    public function __construct(
        public int    $tier,
        public int    $pos,
        public string $type,
        public int    $extraQty,
        public int    $missingQty,
        public int    $resultQty,
        public string $userName,
        public float  $date
    ) {
        parent::__construct();
    }
}