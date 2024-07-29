<?php

namespace api\Core\Models\Item;

class Item {
    /**
     * @param string $barcode
     * @param string $articul
     * @param string $size
     * @param int $price
     * @param int $qty
     * @param bool $marked
     * @param array<ItemCell> $cells
     */
    public function __construct(
        public string $barcode,
        public string $articul,
        public string $size,
        public int $price,
        public int $qty,
        public bool $marked,
        public array $cells
    ) {}
}
