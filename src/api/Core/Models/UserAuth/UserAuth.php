<?php

namespace api\Core\Models\UserAuth;

use api\Core\Models\Modules\ModuleAccess;
use api\Core\Models\Warehouse\Warehouse;

class UserAuth
{
    /** @param array<ModuleAccess> $accessModules */
    public function __construct(
        public UserInfo   $user,
        public Warehouse  $accountWarehouse,
        public ?Warehouse $sessionWarehouse,
        public array      $accessModules,
    ) {}
}