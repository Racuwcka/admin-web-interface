<?php

namespace api\Core\Models\Item;

class ItemCell
{
    public function __construct(
        public $id,
        public $name,
        public $qty
    ) {}
}