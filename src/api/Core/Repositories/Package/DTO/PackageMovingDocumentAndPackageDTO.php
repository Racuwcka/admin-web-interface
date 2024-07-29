<?php

namespace api\Core\Repositories\Package\DTO;

use api\Core\Repositories\DTO;

class PackageMovingDocumentAndPackageDTO extends DTO
{
    public function __construct(
        public string $documentId,
        public string $packageId
    ) {
        parent::__construct();
    }
}