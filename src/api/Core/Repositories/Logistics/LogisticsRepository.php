<?php

namespace api\Core\Repositories\Logistics;

use api\Core\Classes\DataBase;
use api\Core\Models\Input\InputModuleLogistics;
use api\Core\Repositories\Logistics\DTO\LogisticsItemDTO;
use api\Core\Repositories\Logistics\DTO\LogisticsOrderDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\BracketType;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class LogisticsRepository
{
    /**
     * Получение всех позиции зарезервированного заказа под ячейкой
     * @param int $orderId идентификатор заказа
     */
    public static function getItems(int $orderId): int
    {
        try {
            return DataBase::instance()->count(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell_items,
                where: new Where(
                    new CompareOperator(
                        field: 'orderId',
                        value: $orderId,
                        operator: OperationType::Equals
                    ))
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return 0;
        }
    }

    /**
     * Получение количества позиций зарезервированного заказ
     * @param int $orderId идентификатор заказа
     * @return int
     */
    public static function getItemsCount(int $orderId): int
    {
        try {
            return DataBase::instance()->count(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell_items,
                where: new Where(
                    new CompareOperator(
                        field: 'orderId',
                        value: $orderId,
                        operator: OperationType::Equals
                    ))
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return 0;
        }
    }

    /**
     * Удаление товара
     * @param int $itemId идентификатор товара в заказе
     * @return bool
     */
    public static function deleteItem(int $itemId): bool
    {
        return self::deleteItems(key: "itemId", value: $itemId);
    }

    /**
     * Удаление всех товаров пакета
     * @param int $packageId идентификатор пакета логистики
     * @return bool
     */
    public static function deleteItemsPackage(int $packageId): bool
    {
        return self::deleteItems(key: "packageId", value: $packageId);
    }

    /**
     * Удаление товаров ордера
     * @param int $orderId
     * @param array<InputModuleLogistics> $items
     * @return bool
     */
    public static function deleteItemsOrderId(int $orderId, array $items): bool
    {
        try {
            $where = new Where();

            foreach ($items as $item) {
                $where->add(
                    logisticOperatorType: OperatorLogisticType::Or,
                    operator: new CompareOperator(
                        field: 'orderId',
                        value: $orderId,
                        operator: OperationType::Equals,
                        bracket: BracketType::OpenBracket)
                )->add(
                    logisticOperatorType: OperatorLogisticType::And,
                    operator: new CompareOperator(
                        field: 'articul',
                        value: $item->articul,
                        operator: OperationType::Equals)
                )->add(
                    logisticOperatorType: OperatorLogisticType::And,
                    operator: new CompareOperator(
                        field: 'size',
                        value: $item->size,
                        operator: OperationType::Equals,
                        bracket: BracketType::CloseBracket)
                );
            }

            DataBase::instance()->delete(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell_items,
                where: $where
            );

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }

    }

    private static function deleteItems(string $key, $value): bool
    {
        try {
            DataBase::instance()->delete(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell_items,
                where: new Where(
                    new CompareOperator(
                        field: $key,
                        value: $value,
                        operator: OperationType::Equals
                    ))
            );

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Добавление позиции под зарезервированный заказ под ячейкой
     * @param LogisticsItemDTO $dto
     * @return bool
     */
    public static function insertItems(LogisticsItemDTO $dto): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell_items,
                values: $dto->toArray());

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Массовое обновление позиции под зарезервированный заказ под ячейкой
     * @param int $packageId идентификатор пакета логистики
     * @param array<int> $itemIds список идентификаторов itemId
     * @return bool
     */
    public static function updateGroupItems(int $packageId, array $itemIds): bool
    {
        try {
            DataBase::instance()->updateGroup(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell_items,
                updateValues: ["packageId" => $packageId],
                groupField: "itemId",
                groupValues: $itemIds
            );

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Получение списка зарезервированных заказов под ячейками
     * @param string $warehouse идентификатор склада
     * @return array<LogisticsOrderDTO>
     */
    public static function getListOrder(string $warehouse): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell,
                where: new Where(
                    new CompareOperator(
                        field: 'warehouse',
                        value: $warehouse,
                        operator: OperationType::Equals
                    ))
            );
            return LogisticsOrderDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получение зарезервированного заказа под ячейкой
     * @param int $orderId идентификатор заказа
     * @return LogisticsOrderDTO|null
     */
    public static function getOrder(int $orderId): ?LogisticsOrderDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell,
                where: new Where(
                    new CompareOperator(
                        field: 'orderId',
                        value: $orderId,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? LogisticsOrderDTO::fromArray($data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Резервируем ячейку под заказ
     * @param LogisticsOrderDTO $dto
     * @return bool
     */
    public static function insertOrder(LogisticsOrderDTO $dto): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell,
                values: $dto->toArray());

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Удаляем резерв заказа под ячейкой
     * @param int $orderId идентификатор заказа
     * @return bool
     */
    public static function deleteOrder(int $orderId): bool
    {
        try {
            DataBase::instance()->delete(
                type: DataBaseType::data,
                table: DataBaseTable::order_reserve_cell,
                where: new Where(
                    new CompareOperator(
                        field: 'orderId',
                        value: $orderId,
                        operator: OperationType::Equals
                    ))
            );

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Найти незарезервированную ячейку
     * @param string $warehouse идентификатор склада
     * @return string|null идентификатор ячейки
     */
    public static function findUnReservedCell(string $warehouse): ?string
    {
        try {
            $dbData = DataBase::instance()->getDataBaseName(DataBaseType::data);
            $tableCells = DataBaseTable::cells_reserved->value;
            $tableOrder = DataBaseTable::order_reserve_cell->value;

            $requestData = DataBase::instance()->execute(
                query: "SELECT `cellId` FROM $dbData.$tableCells WHERE `warehouse`=? AND `cellId` NOT IN (
                        SELECT `cellId` FROM $dbData.$tableOrder WHERE `warehouse`=?) LIMIT 1",
                args: [$warehouse, $warehouse]);
            return $requestData->fetch(\PDO::FETCH_COLUMN) ?: null;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }
}