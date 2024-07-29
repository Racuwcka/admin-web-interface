<?php

namespace api\Core\Repositories\Access\DTO;

use api\Core\Repositories\DTO;

class AccessTypeDescriptDTO extends DTO
{
    public function __construct(
        public string $typeId,
        public string $descript
    ) {
        parent::__construct();
    }
}