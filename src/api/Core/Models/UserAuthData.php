<?php

namespace api\Core\Models;

use api\Core\Models\UserAuth\UserAuth;

class UserAuthData
{
    public function __construct(
        public string $token,
        public UserAuth $userInfo
    ) {}
}