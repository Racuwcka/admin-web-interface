<?php

namespace api\Core\Repositories\Device\DTO;

use api\Core\Repositories\DTO;

class DeviceHistoryDTO extends DTO
{
    public function __construct(
        public string  $did,
        public int     $operation_type,
        public ?string $warehouse,
        public int  $user,
        public int     $app_version,
        public int     $date,
        public ?int    $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}