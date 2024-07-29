<?php

namespace Requests\Core\Interfaces;

use Requests\Core\AbstractClasses\RequestParams;
use Requests\Core\AbstractClasses\RequestSend;

interface RequestIndex
{
    public function declareParams() : RequestParams;
    public function declareSend() : RequestSend;
}