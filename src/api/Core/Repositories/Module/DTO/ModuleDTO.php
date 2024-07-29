<?php

namespace api\Core\Repositories\Module\DTO;

use api\Core\Repositories\DTO;

class ModuleDTO extends DTO
{
    public function __construct(
        public string $id,
        public string $name,
        public int $active
    ) {
        parent::__construct();
    }
}