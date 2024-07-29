<?php

namespace api\Core\Models\Integration;

class Integration
{
    public function __construct(
        public ?IntegrationResourceData $oneC,
        public ?IntegrationResourceData $logistics
    ) {}
}