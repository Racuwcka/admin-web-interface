<?php

namespace api\Core\Repositories\Session\DTO;

use api\Core\Repositories\DTO;

class SessionDTO extends DTO
{
    public function __construct(
        public string $session_id,
        public int $user_id,
        public string $platform,
        public int $last_activity,
    ) {
        parent::__construct();
    }
}