<?php

namespace api\Core\Repositories\Package\DTO;

use api\Core\Repositories\DTO;

class PackageMovingExternalLocationDTO extends DTO
{
    public function __construct(
        public int $movingId,
        public string $warehouse,
        public string $type,
        public float $date,
        public ?int $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}