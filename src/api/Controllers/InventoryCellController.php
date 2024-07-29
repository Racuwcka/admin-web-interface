<?php

namespace api\Controllers;

use api\Core\Models\Result;
use api\Services\InventoryCellService;

class InventoryCellController
{
    public static function getEventData(): Result
    {
        $data = InventoryCellService::getEventData();
        if (is_null($data)) {
            return Result::success();
        }
        return Result::success($data);
    }

    public static function getFloor($letter, $floor): Result
    {
        $data = InventoryCellService::getFloor($letter, $floor);
        return Result::success($data);
    }

    public static function getShelf($letter, $floor, $shelf): Result
    {
        $data = InventoryCellService::getShelf($letter, $floor, $shelf);
        return Result::success($data);
    }

    public static function searchInEvent($query): Result
    {
        $data = InventoryCellService::searchInEvent($query);
        return Result::success($data);
    }
}