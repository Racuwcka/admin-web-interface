<?php

namespace api\Core\Repositories\Trace\DTO;

use api\Core\Repositories\DTO;

class TraceDTO extends DTO
{
    public function __construct(
        public int    $terminalId,
        public string $name,
    ) {
        parent::__construct();
    }
}