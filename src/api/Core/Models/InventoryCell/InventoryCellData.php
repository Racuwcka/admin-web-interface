<?php

namespace api\Core\Models\InventoryCell;

use api\Core\Models\Cell\Cell;

class InventoryCellData
{
    public function __construct(
        public int $id,
        public Cell $cell,
        public ?InventoryCellEvent $event
    ) {}
}