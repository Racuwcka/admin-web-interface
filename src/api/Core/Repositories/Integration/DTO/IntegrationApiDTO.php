<?php

namespace api\Core\Repositories\Integration\DTO;

use api\Core\Repositories\DTO;

class IntegrationApiDTO extends DTO
{
    public function __construct(
        public string $resource,
        public string $production_url,
        public string $production_login,
        public string $production_password,
        public string $debug_url,
        public string $debug_login,
        public string $debug_password
    ) {
        parent::__construct();
    }
}