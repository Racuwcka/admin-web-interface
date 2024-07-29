<?php

namespace api\Core\Models\InventoryCell;

class InventoryCellFloor
{
    public function __construct(
        public int $id,
        public int $completed,
        public int $total,
        public int $percent
    ) {}
}