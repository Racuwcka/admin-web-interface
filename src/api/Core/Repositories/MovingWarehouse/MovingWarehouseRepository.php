<?php

namespace api\Core\Repositories\MovingWarehouse;

use api\Core\Classes\DataBase;
use api\Core\Repositories\MovingWarehouse\DTO\MovingWarehouseDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class MovingWarehouseRepository
{
    /**
     * Получение списка доступности перемещений между складами, с настройками
     * @param string $warehouse идентификатор склада
     * @return array<MovingWarehouseDTO>
     */
    public static function getMovingWarehouseAccess(string $warehouse): array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::main);
            $requestData = DataBase::instance()->execute(
                query: "SELECT access.warehouseTo as id,
                        warehouse.name as name,
                        warehouse.supportPackage as supportPackage,
                        access.accept as accept
                        FROM $dataBaseName.moving_warehouse_access as access
                        LEFT JOIN $dataBaseName.warehouse ON warehouse.id = access.warehouseTo WHERE 
                        access.warehouseFrom=?",
                args: [$warehouse]
            );

            $data = $requestData->fetchAll();
            return MovingWarehouseDTO::fromArrayToList($data);
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function isMoving(string $uid): ?string
    {
        try {
            $where = new Where();
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'movingUid',
                    value: $uid,
                    operator: OperationType::Equals
                )
            );

            $data = DataBase::instance()->selectOne(
                type: DataBaseType::data,
                table: DataBaseTable::log_moving_warehouse,
                where: $where,
                select_fields: ["createDocumentName"],
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