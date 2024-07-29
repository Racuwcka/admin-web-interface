<?php

namespace Cron\Core\Models;

class CronTask
{
    public function __construct(
        public string $module,
        public string $method,
    ) {}

    public function getParams(): array
    {
        return [
            "module" => $this->module,
            "method" => $this->method
        ];
    }
}