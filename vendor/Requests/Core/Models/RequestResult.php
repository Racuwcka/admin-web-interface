<?php

namespace Requests\Core\Models;

class RequestResult
{
    public function __construct(
        public bool $status,
        public mixed $data = null,
        public string $message = "",
    ) {}
}