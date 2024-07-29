<?php

namespace api\Core\Repositories\Cell\DTO;

use api\Core\Repositories\DTO;

class CellDTO extends DTO
{
    public function __construct(
        public string $id,
        public string $warehouse,
        public string $name,
        public string $letter,
        public int    $floor,
        public int    $shelf,
        public int    $tier,
        public int    $pos
    ) {
        parent::__construct();
    }
}