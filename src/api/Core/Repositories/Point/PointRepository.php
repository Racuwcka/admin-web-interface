<?php

namespace api\Core\Repositories\Point;

use api\Core\Classes\DataBase;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class PointRepository
{
    /**
     * Получение поинтов логистики в соотношение со складом
     * @param string $warehouseId идентификатор склада
     * @return string|null
     */
    public static function getPoint(string $warehouseId): ?string
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::point,
                where: new Where(
                    new CompareOperator(
                        field: 'warehouse',
                        value: $warehouseId,
                        operator: OperationType::Equals
                    )),
                select_fields: ['point'],
                fetchColumn: true
            );

            return $data ?: null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }
}