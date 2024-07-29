<?php

namespace api\Core\Repositories\Region\DTO;

use api\Core\Repositories\DTO;

class RegionDTO extends DTO
{
    public function __construct(
        public string $id,
        public string $name
    ) {
        parent::__construct();
    }
}