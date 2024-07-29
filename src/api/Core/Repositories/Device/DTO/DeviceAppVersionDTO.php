<?php

namespace api\Core\Repositories\Device\DTO;

use api\Core\Repositories\DTO;

class DeviceAppVersionDTO extends DTO
{
    public function __construct(
        public int $app_version,
        public int $devices
    ) {
        parent::__construct();
    }
}