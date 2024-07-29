<?php

namespace api\Core\Repositories\Package\DTO;

class PackageDataAndCountDTO
{
    /**
     * @param array<PackageItemDTO> $list
     * @param int $totalQty
     */
    public function __construct(
        public array $list,
        public int $totalQty
    ) {}
}