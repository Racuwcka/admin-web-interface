<?php

namespace api\Core\Models\Role;

class RoleIdName
{
    public function __construct(
        public int $id,
        public string $name
    ) {}
}