<?php

namespace api\Core\Models\InventoryCell;

use api\Core\Models\Cell\CellQty;

class InventoryCellSearch
{
    /**
     * @param string $size
     * @param int $qty
     * @param array<CellQty> $cells
     */
    public function __construct(
        public string $size,
        public int $qty,
        public array $cells
    ) {}
}