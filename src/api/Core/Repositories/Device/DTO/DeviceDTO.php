<?php

namespace api\Core\Repositories\Device\DTO;

use api\Core\Repositories\DTO;

class DeviceDTO extends DTO
{
    public function __construct(
        public string  $did,
        public int     $app_version,
        public ?string $warehouse,
        public ?int    $user,
        public ?string $last_warehouse,
        public ?int    $last_user,
        public ?int    $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}