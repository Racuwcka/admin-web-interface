<?php

namespace api\Core\Repositories\Cell;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Cell\DTO\CellDTO;
use api\Core\Repositories\Cell\DTO\CellReserveDTO;
use api\Core\Repositories\Cell\DTO\CellShelfCount;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\LikeOperator;

class CellRepository
{
    /**
     * Получение информации о ячейки
     * @param string $id идентификатор ячейки
     * @return CellDTO|null
     */
    public static function get(string $id): ?CellDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::cell,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? CellDTO::fromArray($data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Получить рандомную ячейку
     * @return CellDTO|null
     */
    public static function getRandom(string $warehouse): ?CellDTO
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::main);
            $requestData = DataBase::instance()->execute(
                query: "SELECT * FROM $dataBaseName.cell WHERE warehouse = ? ORDER BY RAND() LIMIT 1",
                args: [$warehouse]);
            $data = $requestData->fetch();
            return $data ? CellDTO::fromArray($data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Получить тип резерва ячейки
     * @param string $id
     * @return CellReserveDTO|null
     */
    public static function checkReserved(string $id): ?CellReserveDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::data,
                table: DataBaseTable::cells_reserved,
                where: new Where(
                    new CompareOperator(
                        field: 'cellId',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? CellReserveDTO::fromArray(data: $data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Получение списка зарезервированных ячеек на текущем складе
     * @param string $warehouse
     * @return array<string>
     */
    public static function getReservedIds(string $warehouse): array
    {
        try {
            $where = new Where();
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: "warehouse",
                    value: $warehouse,
                    operator: OperationType::Equals
                )
            );

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::data,
                table: DataBaseTable::cells_reserved,
                where: $where,
                select_fields: ['cellId'],
                fetchColumn: true
            );

            return $data ?: [];
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получить список всех ячеек в стеллаже
     * @param string $letter буквенное обозначение
     * @param int $floor этаж
     * @param int $shelf стеллаж
     * @return array<CellDTO>
     */
    public static function getListShelf(string $letter, int $floor, int $shelf): array
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'letter',
                    value: $letter,
                    operator: OperationType::Equals
                ));
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'floor',
                    value: $floor,
                    operator: OperationType::Equals
                ))
                ->add(
                    logisticOperatorType: OperatorLogisticType::And,
                    operator: new CompareOperator(
                        field: 'shelf',
                        value: $shelf,
                        operator: OperationType::Equals
                    ));

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::cell,
                where: $where);
            return CellDTO::fromArrayToList(data: $data);
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получение количества ячеек на этаже в каждом стеллаже
     * @param string $letter буквенное обозначение
     * @param int $floor этаж
     * @return array<CellShelfCount>
     */
    public static function getListFloorCount(string $letter, int $floor): array
    {
        try {
            $where = new Where();
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: "letter",
                    value: $letter,
                    operator: OperationType::Equals
                )
            );
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: "floor",
                    value: $floor,
                    operator: OperationType::Equals
                )
            );

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::cell,
                where: $where,
                group_field: "shelf",
                order_value: ["shelf" => "ASC"],
                select_fields: ['shelf', 'COUNT(shelf) AS qty']);

            return CellShelfCount::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получение имени ячейки
     * @param string $id идентификатор ячейки
     * @return string|null
     */
    public static function getName(string $id): ?string
    {
        return self::getField(id: $id, field: 'name');
    }

    private static function getField(string $id, string $field): mixed
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::cell,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    )),
                select_fields: [$field],
                fetchColumn: true
            );

            return $data ?: null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Поиск ячейки по названию
     * @param string $query искомая фраза
     * @return array<CellDTO>
     */
    public static function search(string $query): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::cell,
                where: new Where(
                    new LikeOperator(
                        field: "name",
                        value: "%$query%"
                    )
                ),
                limit: 10);
            return CellDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Принадлежит ли ячейка к переданному складу
     * @param string $id
     * @param string $warehouse
     * @return bool
     */
    public static function onAvailableWarehouse(string $id, string $warehouse): bool
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
                    field: 'warehouse',
                    value: $warehouse,
                    operator: OperationType::Equals
                )
            );

            $count = DataBase::instance()->count(
                type: DataBaseType::main,
                table: DataBaseTable::cell,
                where: $where
            );

            return $count > 0;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}