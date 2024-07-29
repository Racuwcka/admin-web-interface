<?php

namespace api\Core\Repositories\Device\DTO;

use api\Core\Repositories\DTO;

class DeviceCurrentHistoryDTO extends DTO
{
    public function __construct(
        public ?string $warehouse_name,
        public ?string $user_name,
        public int     $app_version,
        public int     $date
    ) {
        parent::__construct();
    }
}