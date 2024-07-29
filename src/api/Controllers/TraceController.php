<?php

namespace api\Controllers;

use api\Core\Models\Result;
use api\Services\TraceService;

class TraceController
{
    public static function getList(): Result
    {
        return Result::success(TraceService::getList());
    }

    public static function download(string $name): Result
    {
        return TraceService::download($name) ? Result::success() : Result::error();
    }
}
