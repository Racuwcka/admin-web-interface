<?php
namespace api\Core\Models\Item;

class ItemData {
    public function __construct(
        public $barcode,
        public $articul,
        public $size,
        public $price,
        public $qty,
        public $categoryName,
        public $categoryId
    ) {}
}
