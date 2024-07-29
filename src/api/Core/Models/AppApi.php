<?php

namespace api\Core\Models;

class AppApi
{
    public function __construct(
        public $url,
        public $login,
        public $password
    ) {}
}