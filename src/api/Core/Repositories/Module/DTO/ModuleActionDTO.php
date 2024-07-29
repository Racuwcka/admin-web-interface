<?php

namespace api\Core\Repositories\Module\DTO;

use api\Core\Repositories\DTO;

class ModuleActionDTO extends DTO
{
    public function __construct(
        public string $actionId,
        public string $name
    ) {
        parent::__construct();
    }
}