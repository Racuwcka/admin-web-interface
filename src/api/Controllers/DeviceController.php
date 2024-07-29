<?php

namespace api\Controllers;

use api\Core\Models\Result;
use api\Services\DeviceService;

class DeviceController {

    public static function getList(): Result
    {
        return Result::success(DeviceService::getList());
    }

    public static function getPercent(): Result
    {
        return Result::success(DeviceService::getPercent());
    }

    public static function getLatestDataList(): Result
    {
        return Result::success(DeviceService::getLatestDataList());
    }

    public static function getHistory(string $id): Result
    {
        return Result::success(DeviceService::getHistory($id));
    }
}
