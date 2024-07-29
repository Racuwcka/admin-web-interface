<?php

namespace api\Core\Repositories\Package;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Package\DTO\PackageDTO;
use api\Core\Repositories\Package\DTO\PackageItemDTO;
use api\Core\Repositories\Package\DTO\PackageMovingDocumentAndPackageDTO;
use api\Core\Repositories\Package\DTO\PackageMovingDTO;
use api\Core\Repositories\Package\DTO\PackageMovingExternalLocationDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class PackageRepository
{
    /**
     * Получение информации об упаковке
     * @param string $id идентификатор упаковки
     * @return ?PackageDTO
     */
    public static function get(string $id): ?PackageDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::data,
                table: DataBaseTable::package,
                where: new Where(
                    new CompareOperator(
                        field: 'packageId',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? PackageDTO::fromArray(data: $data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Получение содержимого упаковки
     * @param string $packageId идентификатор упаковки
     * @param bool $excludeMissing исключать позиции у которых содержимое < 1
     * @return array<PackageItemDTO>
     */
    public static function getData(string $packageId, bool $excludeMissing = false): array
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'packageId',
                    value: $packageId,
                    operator: OperationType::Equals
                ));

            if ($excludeMissing) {
                $where->add(
                    logisticOperatorType: OperatorLogisticType::And,
                    operator: new CompareOperator(
                        field: 'qty',
                        value: 0,
                        operator: OperationType::Greater
                    ));
            }

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::data,
                table: DataBaseTable::package_data,
                where: $where);

            return PackageItemDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получение списка локации перемещения упаковки
     * @param int $movingId идентификатор перемещения
     * @return array<PackageMovingExternalLocationDTO>
     */
    public static function getMovingExternalLocations(int $movingId): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::data,
                table: DataBaseTable::package_moving_external_locations,
                where: new Where(
                    new CompareOperator(
                        field: 'movingId',
                        value: $movingId,
                        operator: OperationType::Equals
                    )),
                order_value: ["id" => "DESC"],
                limit: 10);

            return PackageMovingExternalLocationDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Обновление либо запись содержимого упаковки
     * @param array<PackageItemDTO> $items
     * @return bool
     */
    public static function upsertPackageData(array $items): bool
    {
        try {
            DataBase::instance()->upsert(
                type: DataBaseType::data,
                table: DataBaseTable::package_data,
                values: PackageItemDTO::toList($items)
            );

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Запись новой упаковки
     * @param PackageDTO $data
     * @return bool
     */
    public static function insertPackage(PackageDTO $data): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DataBaseTable::package,
                values: $data->toArray()
            );

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Удаление упаковки
     * @param string $packageId идентификатор упаковки
     * @return bool
     */
    public static function delete(string $packageId): bool
    {
        try {
            DataBase::instance()->delete(
                type: DataBaseType::data,
                table:DataBaseTable::package,
                where: new Where(
                    new CompareOperator(
                        field: 'packageId',
                        value: $packageId,
                        operator: OperationType::Equals
                    ))
            );

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Внесение упаковки в архив
     * @param string $id идентификатор упаковки
     * @return bool
     */
    public static function setArchive(string $id): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::data,
                table: DataBaseTable::package,
                values: ['archive' => 1],
                where: new Where(
                    new CompareOperator(
                        field: 'packageId',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Установка ячейки для упаковки
     * @param string $packageId идентификатор упаковки
     * @param string|null $cell идентификатор ячейки
     * @return bool
     */
    public static function setCell(string $packageId, ?string $cell): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::data,
                table: DataBaseTable::package,
                values: ['archive' => 1, 'cell' => $cell],
                where: new Where(
                    new CompareOperator(
                        field: 'packageId',
                        value: $packageId,
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

    /**
     * Установка склада для упаковки
     * @param string $id идентификатор упаковки
     * @param string|null $warehouse идентификатор склада
     * @return bool
     */
    public static function setWarehouse(string $id, ?string $warehouse): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::data,
                table: DataBaseTable::package,
                values: ['warehouse' => $warehouse],
                where: new Where(
                    new CompareOperator(
                        field: 'packageId',
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

    /**
     * Записываем операцию перемещения упаковки
     * @param PackageMovingDTO $packageMoving
     * @return bool
     */
    public static function insertMoving(PackageMovingDTO $packageMoving): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DataBaseTable::package_moving,
                values: $packageMoving->toArray());

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Обновление статуса перемещения упаковки
     * @param int $movingId идентификатор упаковки
     * @param string $status новый статус
     * @return bool
     */
    public static function updateMovingStatus(int $movingId, string $status): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::data,
                table: DataBaseTable::package_moving,
                values: ["status" => $status],
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $movingId,
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

    /**
     * Обновление статуса внешнего перемещения упаковки
     * @param int $movingId идентификатор упаковки
     * @param string $externalMovingStatus новый внешний статус
     * @return bool
     */
    public static function updateMovingExternalMovingStatus(int $movingId, string $externalMovingStatus): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::data,
                table: DataBaseTable::package_moving,
                values: ["externalMovingStatus" => $externalMovingStatus],
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $movingId,
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

    /**
     * Обновление даты локации внешнего перемещения
     * @param int $id идентификатор локации
     * @return bool
     */
    public static function updateMovingLastExternalLocationDate(int $id): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::data,
                table: DataBaseTable::package_moving_external_locations,
                values: ["date" => microtime(true)],
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

    /**
     * Записываем локацию внешнего перемещения
     * @param int $movingId идентификатор перемещения
     * @param string $warehouse идентификатор склада
     * @param string $type тип локации
     * @return bool
     */
    public static function insertMovingExternalLocation(int $movingId, string $warehouse, string $type): bool
    {
        try {
            $dto = new PackageMovingExternalLocationDTO(
                movingId: $movingId,
                warehouse: $warehouse,
                type: $type,
                date: microtime(true)
            );

            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DataBaseTable::package_moving_external_locations,
                values: $dto->toArray());

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}