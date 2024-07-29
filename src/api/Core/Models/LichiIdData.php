<?php

namespace api\Core\Models;

class LichiIdData
{
    /**
     * @param string $clientId
     * @param array<string> $scopes
     */
    public function __construct(
        public string $clientId,
        public array $scopes
    ) {}
}