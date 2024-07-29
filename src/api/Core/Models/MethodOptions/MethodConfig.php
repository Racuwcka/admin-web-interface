<?php

namespace api\Core\Models\MethodOptions;

class MethodConfig {
    public function __construct(
        public MethodOptions $options,
    ) {}
}