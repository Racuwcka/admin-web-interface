<?php

namespace api\Core\Models\InventoryCell;

class InventoryCellEvent
{
    public function __construct(
        public string $type,
        public int $extraQty,
        public int $missingQty,
        public int $resultQty,
        public string $userName,
        public ?string $userPhoto,
        public int $date
    ) {}
}