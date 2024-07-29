<?php

namespace api\Core\Repositories\Module\DTO;

use api\Core\Repositories\DTO;

class ModuleAccessRuleDTO extends DTO
{
    public function __construct(
        public string $moduleId,
        public string $typeId,
        public int    $exclude,
        public string $value,
        public ?int   $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}