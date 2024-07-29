<?php

namespace api\Core\Repositories\DocumentLock\DTO;

use api\Core\Repositories\DTO;

class DocumentLockDTO extends DTO
{
    public function __construct(
        public string $id,
        public string $did,
        public string $user,
        public int    $date,
        public ?int   $i = null
    ) {
        parent::__construct(increment: 'i');
    }
}