<?php

namespace api\Core\Models\UserAuth;

class Auth
{
    public function __construct(
        public ?UserAuth $data,
        public AuthInfo  $info
    ) {}
}