<?php

namespace api\Core\Models\Cell;

class CellLogAction
{
    public function __construct(
        public $action,
        public $type,
        public $operationId,
        public $descript,
        public $qty,
        public $userName,
        public $date) {}

}