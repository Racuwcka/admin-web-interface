<?php

namespace api\Core\Repositories\Cell\DTO;

use api\Core\Repositories\DTO;

class CellShelfCount extends DTO
{
    public function __construct(
        public int $shelf,
        public int $qty
    )
    {
        parent::__construct();
    }
}