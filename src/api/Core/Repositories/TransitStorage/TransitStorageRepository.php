<?php

namespace api\Core\Repositories\TransitStorage;

use api\Core\Classes\DataBase;
use api\Core\Repositories\TransitStorage\DTO\TransitStorageDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class TransitStorageRepository
{
    /**
     * Записываем операцию в транзитном хранении
     * @param TransitStorageDTO $transitStorageDTO
     * @return bool
     */
    public static function insert(TransitStorageDTO $transitStorageDTO): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DataBaseTable::transit_storage,
                values: $transitStorageDTO->toArray()
            );

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Получение операции транзитного хранения
     * @param string $code идентификатор заказа
     * @return TransitStorageDTO|null
     */
    public static function get(string $code): ?TransitStorageDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::data,
                table: DataBaseTable::transit_storage,
                where: new Where(
                    new CompareOperator(
                        field: 'orderId',
                        value: $code,
                        operator: OperationType::Equals
                    ))
            );

            return $data ? TransitStorageDTO::fromArray($data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }
}