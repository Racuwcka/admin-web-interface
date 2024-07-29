<?php

namespace Database\Core\Models;

class OperatorRelease
{
    public function __construct(
        public string $query,
        public array $params
    ) {}
}