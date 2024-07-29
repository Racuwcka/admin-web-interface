<?php

namespace api\Core\Repositories\LogLogisticsUnreserve;

use api\Core\Classes\DataBase;
use api\Core\Models\Input\InputModuleLogistics;
use api\Core\Repositories\LogLogisticsUnreserve\DTO\LogLogisticsUnreserveDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class LogLogisticsUnreserveRepository
{
    /** Записываем операцию удаления заказа в логистике */
    public static function insert(int $orderId, string $status, array $items, int $remainsItem): bool
    {
        try {
            $articuls = [];
            $sizes = [];

            foreach ($items as $item) {
                $articuls[] = $item->articul;
                $sizes[] = $item->size;
            }

            $dto = new LogLogisticsUnreserveDTO(
                status: $status,
                orderId: $orderId,
                articul: json_encode($articuls),
                size: json_encode($sizes),
                remainsItem: $remainsItem,
                date: date('Y-m-d H:i:s'));

            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DataBaseTable::log_logistics_unreserve,
                values: $dto->toArray());

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function update(int $id, string $status, ?int $remainsItem): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::data,
                table: DataBaseTable::log_logistics_unreserve,
                values: ['status' => $status, 'remainsItem' => $remainsItem],
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    )
                ));

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}