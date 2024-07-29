<?php

namespace api\Core\Repositories\InventoryCell\DTO;

use api\Core\Repositories\DTO;

class InventoryCellActionResultDTO extends DTO
{
    public function __construct(
        public int     $extraQty,
        public int     $missingQty,
        public int     $resultQty,
        public string  $userId,
        public float   $date,
    ) {
        parent::__construct();
    }
}