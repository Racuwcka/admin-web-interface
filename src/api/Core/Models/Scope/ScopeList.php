<?php

namespace api\Core\Models\Scope;

class ScopeList
{
    public function __construct(
        public ScopeId    $id,
        public ScopeName  $info,
        public ScopePhoto $photo,
        public ScopeData  $data
    ) {}
}