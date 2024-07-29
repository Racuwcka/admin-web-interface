<?php

namespace api\Core\Models\Cell;

class CellQty
{
    public function __construct(
        public $id,
        public $name,
        public $qty = 0) {}
}