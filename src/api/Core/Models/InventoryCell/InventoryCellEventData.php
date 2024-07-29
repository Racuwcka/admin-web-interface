<?php

namespace api\Core\Models\InventoryCell;

class InventoryCellEventData
{
    public function __construct(
        public int $qty_product = 0,
        public int $qty_cells = 0,
        public int $qty_users = 0,
        public int $qty_extra = 0,
        public int $qty_missing = 0,
        public int $qty_days = 0,
        public int $qty_percent_discrepancy = 0
    ) {}
}