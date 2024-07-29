<?php

namespace Database\Core\Models;

class WhereRelease
{
    public function __construct(
        public readonly string $query,
        public readonly array $params
    ) {}
}