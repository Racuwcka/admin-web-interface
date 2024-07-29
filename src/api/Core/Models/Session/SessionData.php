<?php

namespace api\Core\Models\Session;

class SessionData
{
    public function __construct(public ?string $warehouse) {}
}