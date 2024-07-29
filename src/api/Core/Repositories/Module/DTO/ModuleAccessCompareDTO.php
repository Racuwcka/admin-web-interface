<?php

namespace api\Core\Repositories\Module\DTO;

use api\Core\Repositories\DTO;

class ModuleAccessCompareDTO extends DTO
{
    public function __construct(
        public string $moduleId,
        public string $typeId,
        public string $compareId,
        public ?int   $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}