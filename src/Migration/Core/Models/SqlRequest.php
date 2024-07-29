<?php

namespace Migration\Core\Models;

class SqlRequest
{
    public function __construct(
        public string $query,
        public array $args = []
    ) {}
}
