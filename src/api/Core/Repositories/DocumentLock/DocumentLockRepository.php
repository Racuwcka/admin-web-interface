<?php

namespace api\Core\Repositories\DocumentLock;

use api\Core\Classes\DataBase;
use api\Core\Repositories\DocumentLock\DTO\DocumentLockDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class DocumentLockRepository
{
    /**
     * Получение блокировки документа
     * @param string $id идентификатор документа
     * @return DocumentLockDTO|null
     */
    public static function get(string $id): ?DocumentLockDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::data,
                table: DataBaseTable::documents_lock,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? DocumentLockDTO::fromArray($data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Блокируем документ
     * @param DocumentLockDTO $documentLockDTO
     * @return bool
     */
    public static function lock(DocumentLockDTO $documentLockDTO): bool
    {
        try {
            DataBase::instance()->upsert(
                type: DataBaseType::data,
                table: DataBaseTable::documents_lock,
                values: [$documentLockDTO->toArray()]
            );

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Разблокировать документ
     * @param string $id
     * @param string $did
     * @return bool
     */
    public static function deleteIdDid(string $id, string $did): bool
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'id',
                    value: $id,
                    operator: OperationType::Equals
                ));
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'did',
                    value: $did,
                    operator: OperationType::Equals
                ));

            DataBase::instance()->delete(
                type: DataBaseType::data,
                table: DataBaseTable::documents_lock,
                where: $where);

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Разблокировать все документы заблокированных под устройством
     * @param string $did
     * @return bool
     */
    public static function deleteDid(string $did): bool
    {
        try {
            DataBase::instance()->delete(
                type: DataBaseType::data,
                table: DataBaseTable::documents_lock,
                where: new Where(
                    new CompareOperator(
                        field: 'did',
                        value: $did,
                        operator: OperationType::Equals
                    ))
            );

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}