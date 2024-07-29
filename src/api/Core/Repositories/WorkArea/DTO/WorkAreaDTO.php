<?php

namespace api\Core\Repositories\WorkArea\DTO;

use api\Core\Repositories\DTO;

class WorkAreaDTO extends DTO
{
    public function __construct(
        public string $name,
        public string $warehouse,
        public ?int $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}