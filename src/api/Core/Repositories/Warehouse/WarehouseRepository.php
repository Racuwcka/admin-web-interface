<?php

namespace api\Core\Repositories\Warehouse;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Warehouse\DTO\WarehouseDTO;
use api\Core\Repositories\Warehouse\DTO\WarehouseIdNameDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\LikeOperator;

class WarehouseRepository
{
    /**
     * Получить склад
     * @param string $id идентификатор склада
     * @return WarehouseDTO|null
     */
    public static function get(string $id): ?WarehouseDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::warehouse,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? WarehouseDTO::fromArray(data: $data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /** @return array<WarehouseDTO> */
    public static function search(string $name): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::warehouse,
                where: new Where(
                    new LikeOperator(
                        field: 'name',
                        value: "%$name%"
                    ))
            );
            return WarehouseDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<WarehouseDTO> */
    public static function getByConsolidated(?bool $consolidated = null): array
    {
        try {
            if (!is_null($consolidated)) {
                $where = new Where(
                    new CompareOperator(
                        field: 'consolidated',
                        value: intval($consolidated),
                        operator: OperationType::Equals
                    ));
            }

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::warehouse,
                where: $where ?? null,
                order_value: ['id' => 'ASC']
            );
            return WarehouseDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получить имя склада
     * @param string $id идентификатор склада
     * @return string|null
     */
    public static function getName(string $id): ?string
    {
        return self::getField(id: $id, field: 'name');
    }

    /**
     * Получить регион склада
     * @param string $id идентификатор склада
     * @return string|null
     */
    public static function getRegion(string $id): ?string
    {
        return self::getField(id: $id, field: 'region');
    }

    /**
     * Получить признак консолидации склада
     * @param string $id идентификатор склада
     * @return int|null
     */
    public static function getConsolidated(string $id): ?int
    {
        return self::getField(id: $id, field: 'consolidated');
    }

    /**
     * Получить признак поддержки упаковок на складе
     * @param string $id идентификатор склада
     * @return int|null
     */
    public static function getSupportPackage(string $id): ?int
    {
        return self::getField(id: $id, field: 'supportPackage');
    }

    /**
     * Получение родительского склада
     * @param string $id идентификатор склада
     * @return WarehouseDTO|null
     */
    public static function getParent(string $id): ?WarehouseDTO
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::main);

            $requestData = DataBase::instance()->execute(
                query: "SELECT * FROM $dataBaseName.warehouse WHERE `id` = (SELECT `parent` FROM $dataBaseName.warehouse WHERE `id`=?) AND `virtual` = 1",
                args: [$id]
            );
            $data = $requestData->fetch();
            return $data ? WarehouseDTO::fromArray(data: $data) : null;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Получение идентификатора и имени склада
     * @param string $id идентификатор склада
     * @return WarehouseIdNameDTO|null
     */
    public static function getIdName(string $id): ?WarehouseIdNameDTO
    {
        $data = self::getFields($id, ['id', 'name']);

        if (is_null($data)) {
            return null;
        }

        return new WarehouseIdNameDTO(
            id: $data['id'],
            name: $data['name']
        );
    }

    /**
     * Получить склад, с равным родителем
     * @param string $id идентификатор склада
     * @param string $parent идентификатор родителя
     * @return WarehouseDTO|null
     */
    public static function getThisParent(string $id, string $parent): ?WarehouseDTO
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
                    field: 'parent',
                    value: $parent,
                    operator: OperationType::Equals
                )
            );

            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::warehouse,
                where: $where
            );
            return $data ? WarehouseDTO::fromArray(data: $data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /** @return array<WarehouseIdNameDTO> */
    public static function getByParent(string $id): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::warehouse,
                where: new Where(
                    new CompareOperator(
                        field: 'parent',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );
            return WarehouseIdNameDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function exists(string $id): bool
    {
        try {
            $count = DataBase::instance()->count(
                type: DataBaseType::main,
                table: DataBaseTable::warehouse,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    )
                )
            );

            return $count > 0;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    private static function getField(string $id, string $field): mixed
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::warehouse,
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

    private static function getFields(string $id, array $fields): ?array
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::warehouse,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    )),
                select_fields: $fields
            );

            return $data ?: null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }
}