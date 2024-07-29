<?php

namespace Requests\Modules\LID;

use Requests\Core\AbstractClasses\RequestParams;

class RequestLIDParams extends RequestParams
{
    public string $clientId;

    public function __construct() {}

    public function set(
        string $clientId
    ): void
    {
        $this->clientId = $clientId;
    }
}