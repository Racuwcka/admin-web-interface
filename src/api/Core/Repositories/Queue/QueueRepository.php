<?php

namespace api\Core\Repositories\Queue;

use api\Core\Classes\DataBase;
use api\Core\Enums\Queue\QueueStatusType;
use api\Core\Repositories\Queue\DTO\QueueDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorEntryType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\EntryOperator;

class QueueRepository
{
    public static function create(QueueDTO $queueDTO): ?int
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::mdm,
                table: DataBaseTable::queue,
                values: $queueDTO->toArray()
            );

            return DataBase::instance()->lastInsertId();
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /** @return array<QueueDTO> */
    public static function get(
        string $name,
        int $userId,
        int $page,
        int $limit): array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::mdm);
            $table = DataBaseTable::queue->value;

            $request = DataBase::instance()->execute(
                query: "SELECT * FROM $dataBaseName.$table
                        WHERE name = '$name' AND user_id = $userId ORDER BY id DESC LIMIT ? OFFSET ? ",
                args: [
                    $limit,
                    $limit * ($page - 1)
                ]);
            $data = $request->fetchAll();
            return QueueDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function update(int $id, QueueStatusType $statusType): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::mdm,
                table: DataBaseTable::queue,
                values: ['status' => $statusType->value],
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals))
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function updateBusy(array $ids): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::mdm,
                table: DataBaseTable::queue,
                values: ['status' => QueueStatusType::busy->value],
                where: new Where(
                    new EntryOperator(
                        field: 'id',
                        value: $ids,
                        type: OperatorEntryType::In))
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function getCount(int $userId): int
    {
        try {
            return DataBase::instance()->count(
                type: DataBaseType::mdm,
                table: DataBaseTable::queue,
                where: new Where(
                    new CompareOperator(
                        field: 'user_id',
                        value: $userId,
                        operator: OperationType::Equals))
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return 0;
        }
    }
}