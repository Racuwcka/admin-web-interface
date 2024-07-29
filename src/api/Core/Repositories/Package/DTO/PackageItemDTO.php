<?php

namespace api\Core\Repositories\Package\DTO;

use api\Core\Repositories\DTO;

class PackageItemDTO extends DTO
{
    public function __construct(
        public string $packageId,
        public string $hash,
        public string $barcode,
        public string $articul,
        public string $size,
        public int $qty,
        public ?int $id = null
    ) {
        parent::__construct();
    }
}