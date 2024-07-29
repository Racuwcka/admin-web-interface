<?php

namespace api\Core\Repositories\Warehouse\DTO;

use api\Core\Repositories\DTO;

class WarehouseDTO extends DTO
{
    public function __construct(
        public string $id,
        public string $name,
        public int $address,
        public int $retail,
        public int $reciept,
        public int $storage,
        public string $region,
        public int $consolidated,
        public int $virtual,
        public ?string $parent,
        public int $supportPackage,
        public int $supportAcceptPackage,
        public ?int $i = null
    ) {
        parent::__construct(increment: 'i');
    }
}