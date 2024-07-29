<?php

namespace api\Core\Models\Cell;

use api\Core\Enums\CellReservedType;
use api\Core\Models\Document\DocumentReserve;
use api\Core\Models\IdValue;
use api\Core\Models\Item\Item;

class Cell
{
    /**
     * @param string $name
     * @param string $id
     * @param IdValue $warehouse
     * @param int $qtyItems
     * @param int $qtyItemsRezerve
     * @param array<Item> $items
     * @param array<Item> $itemsRezerve
     * @param array<DocumentReserve> $rezerveDocuments
     * @param array<Item> $rezerveInvalid
     * @param array<Item> $incorrectPositions
     * @param bool $incorrectItems
     * @param array $actions
     * @param CellReservedType | null $reserved
     * @param CellLogAction | null $lastLog
     */
    public function __construct(
        public string $name,
        public string $id,
        public IdValue $warehouse,
        public int $qtyItems,
        public int $qtyItemsRezerve,
        public array $items,
        public array $itemsRezerve,
        public array $rezerveDocuments,
        public array $rezerveInvalid,
        public array $incorrectPositions,
        public bool $incorrectItems,
        public array $actions,
        public ?CellReservedType $reserved,
        public ?CellLogAction $lastLog) {}
}