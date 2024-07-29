<?php

namespace api\Core\Repositories\WorkArea;

use api\Core\Classes\DataBase;
use api\Core\Repositories\WorkArea\DTO\WorkAreaDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\LikeOperator;

class WorkAreaRepository
{
    /**
     * Получить рабочую зону
     * @param int $id идентификатор рабочей зоны
     * @return ?WorkAreaDTO
     */
    public static function get(int $id): ?WorkAreaDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::work_area,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? WorkAreaDTO::fromArray($data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Получить все рабочие зоны
     * @return array<WorkAreaDTO>
     */
    public static function getListAll(): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::work_area
            );

            return $data ? WorkAreaDTO::fromArrayToList($data) : [];
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<WorkAreaDTO> */
    public static function search(string $name): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::work_area,
                where: new Where(
                    new LikeOperator(
                        field: 'name',
                        value: "%$name%"
                    ))
            );
            return WorkAreaDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получить список идентификаторов модулей, доступных для рабочей зоны
     * @param int $id идентификатор рабочей зоны
     * @return array<string>
     */
    public static function getListModuleId(int $id): array
    {
        try {
            return DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::work_area_modules,
                where: new Where(
                    new CompareOperator(
                        field: 'workAreaId',
                        value: $id,
                        operator: OperationType::Equals
                    )),
                select_fields: ['moduleId'],
                fetchColumn: true
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получить список идентификаторов действий модуля, доступных для рабочей зоны
     * @param string $id идентификатор рабочей зоны
     * @param string $moduleId идентификатор модуля
     * @return array<string>
     */
    public static function getModuleActionIds(string $id, string $moduleId): array
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'workAreaId',
                    value: $id,
                    operator: OperationType::Equals
                ));
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'moduleId',
                    value: $moduleId,
                    operator: OperationType::Equals
                )
            );

            return DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::work_area_modules_action,
                where: $where,
                select_fields: ['actionId'],
                fetchColumn: true
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }
}