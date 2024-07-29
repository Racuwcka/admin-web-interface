<?php

namespace api\Core\Models\Document;

use api\Core\Models\Item\Item;

class DocumentReserve
{
    /**
     * @param string $id
     * @param string $name
     * @param string $description
     * @param int $qty
     * @param array<Item> $items
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public int $qty,
        public array $items) {}
}