<?php

namespace api\Core\Repositories\Nomenclature\DTO;

use api\Core\Repositories\DTO;

class NomenclatureDTO extends DTO
{
    public function __construct(
        public string $name,
        public string $barcode,
        public string $articul,
        public string $size,
        public int $marked
    ) {
        parent::__construct();
    }
}