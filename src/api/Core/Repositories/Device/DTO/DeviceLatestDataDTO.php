<?php

namespace api\Core\Repositories\Device\DTO;

use api\Core\Repositories\DTO;

class DeviceLatestDataDTO extends DTO
{
    public function __construct(
        public int     $id,
        public int     $appVersion,
        public ?string $warehouseName,
        public ?string $userName,
        public ?string $photo,
    ) {
        parent::__construct();
    }
}