<?php

namespace api\Core\Repositories\Package\DTO;

use api\Core\Repositories\DTO;

class PackageDTO extends DTO
{
    public function __construct(
        public string $packageId,
        public int $archive,
        public ?string $warehouse,
        public ?string $cell,
        public ?int $id = null
    ) {
        parent::__construct(increment: "id");
    }
}