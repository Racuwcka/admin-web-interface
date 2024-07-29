<?php

namespace api\Core\Models\Account;

use api\Core\Models\Role\RoleIdName;
use api\Core\Models\Warehouse\WarehouseIdName;

class Account
{
    public function __construct(
        public int              $id,
        public string           $firstName,
        public string           $lastName,
        public string           $secondName,
        public ?string          $photo,
        public ?string          $birthday,
        public ?string          $gender,
        public ?RoleIdName      $role,
        public ?WarehouseIdName $warehouse,
        public bool             $active,
    ) {}
}