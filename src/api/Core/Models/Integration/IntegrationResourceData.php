<?php

namespace api\Core\Models\Integration;

class IntegrationResourceData
{
    public function __construct(
        public string $production_url,
        public string $production_login,
        public string $production_password,
        public string $debug_url,
        public string $debug_login,
        public string $debug_password
    ) {}
}