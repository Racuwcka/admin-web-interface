<?php

namespace api\Core\Repositories\Package\DTO;

use api\Core\Repositories\DTO;

class PackageMovingDTO extends DTO
{
    public function __construct(
        public string $packageId,
        public string $documentType,
        public string $documentId,
        public string $documentName,
        public string $warehouseFrom,
        public ?string $warehouseTo,
        public string $status,
        public ?string $externalMovingStatus,
        public ?int $id = null,
    ) {
        parent::__construct(increment: 'id');
    }
}