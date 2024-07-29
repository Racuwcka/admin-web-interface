<?php

namespace api\Core\Repositories\Access\DTO;

use api\Core\Repositories\DTO;

class AccessTypeValueDTO extends DTO
{
    public function __construct(
        public string $typeId,
        public string $typeValue
    ) {
        parent::__construct();
    }
}