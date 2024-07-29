<?php

namespace api\Core\Models\Scope;

use api\Core\Enums\ScopeType;
use api\Core\Interfaces\ScopeInterface;

class Scope {
    public function __construct(
        public ScopeType $type,
        public ScopeInterface $data
    ) {}
}