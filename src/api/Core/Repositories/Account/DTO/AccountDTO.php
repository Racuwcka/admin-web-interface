<?php

namespace api\Core\Repositories\Account\DTO;

use api\Core\Repositories\DTO;

class AccountDTO extends DTO
{
    public function __construct(
        public ?string $auth_id,
        public ?int    $bx_id,
        public ?string $first_name,
        public ?string $last_name,
        public ?string $second_name,
        public ?string $photo,
        public ?string $birthday,
        public ?string $gender,
        public ?int    $roleId,
        public ?string $warehouse,
        public int     $active,
        public ?int    $id = null
    ) {
        parent::__construct(increment: 'id');
    }
}