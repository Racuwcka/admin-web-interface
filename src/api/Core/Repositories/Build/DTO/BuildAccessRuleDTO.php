<?php

namespace api\Core\Repositories\Build\DTO;

use api\Core\Repositories\DTO;

class BuildAccessRuleDTO extends DTO
{
    public function __construct(
        public int $buildId,
        public string $typeId,
        public int $exclude,
        public string $value,
        public ?int $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}