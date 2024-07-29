<?php

namespace api\Core\Repositories\Queue\DTO;

use api\Core\Repositories\DTO;

class QueueDTO extends DTO
{
    public function __construct(
        public string  $name,
        public int     $user_id,
        public string  $status,
        public int     $count,
        public string  $created_at,
        public ?int    $id = null
    ) {
        parent::__construct(increment: "id");
    }
}