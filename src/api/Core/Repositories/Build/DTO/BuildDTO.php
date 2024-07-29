<?php

namespace api\Core\Repositories\Build\DTO;

use api\Core\Repositories\DTO;

class BuildDTO extends DTO
{
    public function __construct(
        public string $name,
        public string $descript,
        public int $active,
        public int $date,
        public ?int $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}