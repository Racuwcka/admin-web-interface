<?php

namespace api\Core\Repositories\Module\DTO;

use api\Core\Repositories\DTO;

class ModuleAllActionDTO extends DTO
{
    public function __construct(
        public string  $moduleId,
        public ?string $actionId,
        public ?string $type,
        public ?string $name,
        public ?int    $groupId,
        public ?string $groupName,
        public string  $moduleName,
        public ?int    $active
    ) {
        parent::__construct();
    }
}