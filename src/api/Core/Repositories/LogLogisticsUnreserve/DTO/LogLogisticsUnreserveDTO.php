<?php

namespace api\Core\Repositories\LogLogisticsUnreserve\DTO;

use api\Core\Repositories\DTO;

class LogLogisticsUnreserveDTO extends DTO
{
    public function __construct(
        public string $status,
        public int    $orderId,
        public string $articul,
        public string $size,
        public int    $remainsItem,
        public string $date,
        public ?int   $id = null
    ) {
        parent::__construct(increment: "id");
    }
}