<?php

namespace api\Controllers;

use api\Core\Models\Input\InputModuleLogistics;
use api\Core\Models\Result;
use api\Services\LogisticsService;

class LogisticsController {

    public static function unreserveCell(int $orderId, string $items): Result
    {
        $inputItemLogisticsList = InputModuleLogistics::fromJsonList($items);

        if (count($inputItemLogisticsList) < 1) {
            return Result::error('invalid.access.modules.parameters');
        }

        if (!LogisticsService::unreserveCell($orderId, $inputItemLogisticsList)) {
            return Result::error('error.delete.reserved.cells');
        }

        return Result::success();
    }
}
