<?php

namespace api\Core\Repositories\Cell\DTO;

use api\Core\Repositories\DTO;

class CellReserveDTO extends DTO
{
    public function __construct(
        public string $type,
        public string $warehouse,
        public string $cellId,
        public ?int $id = null
    ) {
        parent::__construct(increment: "id");
    }
}