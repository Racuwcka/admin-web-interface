<?php

namespace api\Core\Repositories\Role\DTO;

use api\Core\Repositories\DTO;

class RoleActionsDTO extends DTO
{
    public function __construct(
        public string $moduleId,
        public string $actionId
    ) {
        parent::__construct();
    }
}