<?php

namespace Requests\Core\Interfaces;

use Requests\Core\AbstractClasses\RequestParams;

interface RequestSend
{
    function setParams(RequestParams $params): void;
    function issetParams() : bool;
}