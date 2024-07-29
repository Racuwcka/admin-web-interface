<?php

namespace api\Services;

use api\Core\Classes\DataBase;
use api\Core\Models\Input\InputModuleLogistics;
use api\Core\Repositories\Logistics\LogisticsRepository;
use api\Core\Repositories\LogLogisticsUnreserve\LogLogisticsUnreserveRepository;

class LogisticsService
{
    /** @param array<InputModuleLogistics> $items */
    public static function unreserveCell(int $orderId, array $items): bool
    {
        try {
            $sourceItems = LogisticsRepository::getItems($orderId);

            LogLogisticsUnreserveRepository::insert(orderId: $orderId, status: 'wait', items: $items, remainsItem: $sourceItems);
            $logId = DataBase::instance()->lastInsertId();

            DataBase::instance()->beginTransaction();

            LogisticsRepository::deleteItemsOrderId(orderId: $orderId, items: $items);

            if (DataBase::instance()->rowCount() == 0) {
                LogLogisticsUnreserveRepository::update(id: $logId, status: 'absent', remainsItem: 0);
                DataBase::instance()->commit();
                return true;
            }

            $remainsItem = LogisticsRepository::getItems($orderId);
            if (!$remainsItem) {
                LogisticsRepository::deleteOrder($orderId);
            }

            LogLogisticsUnreserveRepository::update(id: $logId, status: 'success', remainsItem: $remainsItem);
            DataBase::instance()->commit();

            return true;
        }
        catch (\Throwable $e) {
            DataBase::instance()->rollBack();

            if (isset($logId)) {
                LogLogisticsUnreserveRepository::update(id: $logId, status: 'error', remainsItem: $sourceItems ?? 0);
            } else {
                LogLogisticsUnreserveRepository::insert(orderId: $orderId, status: 'error', items: $items, remainsItem: $sourceItems ?? 0);
            }
            ThrowableLogger::catch($e);
            return false;
        }
    }
}