<?php

namespace api\Core\Repositories\Account\DTO;

use api\Core\Repositories\DTO;

class PersonalizationAppDTO extends DTO
{
    public function __construct(
        public int $userId,
        public ?string $themeMode,
        public ?string $textSize
    ) {
        parent::__construct();
    }
}