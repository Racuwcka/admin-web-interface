<?php

namespace api\Core\Repositories\Build;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Build\DTO\BuildAccessRuleDTO;
use api\Core\Repositories\Build\DTO\BuildDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class BuildRepository
{
    /**
     * Получение информации о сборке
     * @param int $id Идентификатор сборки
     * @return ?BuildDTO
     */
    public static function get(int $id): ?BuildDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::build,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? BuildDTO::fromArray(data: $data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Получение списка идентификаторов всех сборок
     * @param ?bool $active признак активности, null - все
     * @return array<int>
     */
    public static function getListId(?bool $active = null): array
    {
        try {
            $where = null;
            if (!is_null($active)) {
                $where = new Where(
                    new CompareOperator(
                        field: 'active',
                        value: $active ? 1 : 0,
                        operator: OperationType::Equals
                    )
                );
            }

            return DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::build,
                where: $where,
                select_fields: ['id'],
                fetchColumn: true
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получение всего списка правил доступности сборок
     * @return array<BuildAccessRuleDTO>
     */
    public static function getListAccess(): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::build_access
            );
            return BuildAccessRuleDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }
}