<?php

namespace api\Core\Models\Modules;

class ModuleAccess
{
    public function __construct(
        public string $module,
        public array $actions
    ) {}
}