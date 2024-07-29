<?php

namespace Requests\Core\AbstractClasses;

use Requests\Core\Interfaces\RequestLogger;

abstract class RequestSend implements \Requests\Core\Interfaces\RequestSend
{
    protected readonly RequestParams $params;
    protected readonly RequestLogger $logger;

    public function setParams(RequestParams $params): void
    {
        $this->params = $params;
    }

    public function setLogger(RequestLogger $logger): void
    {
        $this->logger = $logger;
    }

    public function issetParams() : bool
    {
        return isset($this->params);
    }
}