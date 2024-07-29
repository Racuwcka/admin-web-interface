<?php

namespace api\Core\Models\Session;

use api\Core\Models\User;
use api\Core\Models\Warehouse\Warehouse;

class Session
{
    public function __construct(
        public string       $id,
        public User         $user,
        public ?Warehouse   $warehouse
    ) {}
}