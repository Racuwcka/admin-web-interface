<?php

namespace Requests\Core\AbstractClasses;

abstract class RequestPart
{
    public function __construct(public RequestSend $send) {}
}