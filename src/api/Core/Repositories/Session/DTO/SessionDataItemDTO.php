<?php

namespace api\Core\Repositories\Session\DTO;

use api\Core\Repositories\DTO;

class SessionDataItemDTO extends DTO
{
    public function __construct(
        public string  $data_id,
        public string  $session_id,
        public string  $attr,
        public ?string $value,
        public ?int    $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}