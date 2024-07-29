<?php

namespace api\Core\Models\MethodOptions;

class MethodOptions {
    public function __construct(
        public bool $requiredWarehouse
    ) {}
}