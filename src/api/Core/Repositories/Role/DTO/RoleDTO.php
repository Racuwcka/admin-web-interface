<?php

namespace api\Core\Repositories\Role\DTO;

use api\Core\Repositories\DTO;

class RoleDTO extends DTO
{
    public function __construct(
        public string $name,
        public string $description,
        public int    $active,
        public ?int   $id = null
    ) {
        parent::__construct(increment: $id);
    }
}