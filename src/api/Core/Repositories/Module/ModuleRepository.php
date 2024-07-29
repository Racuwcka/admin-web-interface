<?php

namespace api\Core\Repositories\Module;

use api\Core\Classes\DataBase;
use api\Core\Models\Input\InputModuleAccess;
use api\Core\Models\Input\InputModuleAccessCompare;
use api\Core\Repositories\Module\DTO\ModuleAccessCompareDTO;
use api\Core\Repositories\Module\DTO\ModuleAccessRuleDTO;
use api\Core\Repositories\Module\DTO\ModuleActionDTO;
use api\Core\Repositories\Module\DTO\ModuleAllActionDTO;
use api\Core\Repositories\Module\DTO\ModuleDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\LikeOperator;

class ModuleRepository
{
    /**
     * Получение всех идентификаторов модулей
     * @param bool|null $active признак активности, null - все
     * @return array<string>
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
                    ));
            }

             return DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::modules,
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

    public static function isset(string $moduleId): ?bool
    {
        try {
            return (bool) DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::modules,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $moduleId,
                        operator: OperationType::Equals)
                ));
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }

    }

    /** @return array<ModuleDTO> */
    public static function getList(?string $name): array
    {
        try {
            if (!is_null($name)) {
                $where = new Where(
                    new LikeOperator(
                        field: 'name',
                        value: "%$name%")
                );
            }

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::modules,
                where: $where ?? null
            );

            return ModuleDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получение всего списка сравнения доступности модулей
     * @return array<ModuleAccessRuleDTO>
     */
    public static function getListAccess(?string $moduleId = null): array
    {
        try {
            if (!is_null($moduleId)) {
                $where = new Where(
                    new CompareOperator(
                        field: 'moduleId',
                        value: $moduleId,
                        operator: OperationType::Equals)
                );
            }
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::modules_access,
                where: $where ?? null
            );
            return ModuleAccessRuleDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @param array<InputModuleAccess> $input */
    public static function insertAccess(array $input): bool
    {
        try {
            DataBase::instance()->insertMultiple(
                type: DataBaseType::main,
                table: DataBaseTable::modules_access,
                values: InputModuleAccess::toList($input)
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /** @param array<InputModuleAccessCompare> $input */
    public static function insertAccessCompare(array $input): bool
    {
        try {
            DataBase::instance()->insertMultiple(
                type: DataBaseType::main,
                table: DataBaseTable::modules_access_compare,
                values: InputModuleAccessCompare::toList($input)
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function deleteAccess(string $moduleId): bool
    {
        try {
            DataBase::instance()->delete(
                type: DataBaseType::main,
                table: DataBaseTable::modules_access,
                where: new Where(
                    new CompareOperator(
                        field: 'moduleId',
                        value: $moduleId,
                        operator: OperationType::Equals)
                ));
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function deleteAccessCompare(string $moduleId): bool
    {
        try {
            DataBase::instance()->delete(
                type: DataBaseType::main,
                table: DataBaseTable::modules_access_compare,
                where: new Where(
                    new CompareOperator(
                        field: 'moduleId',
                        value: $moduleId,
                        operator: OperationType::Equals)
                ));
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * @param string $moduleId
     * @return array<ModuleActionDTO>
     */
    // TODO Поменять название на getActionsIdName и название модели, также сделать в Бэке app
    public static function getActions(string $moduleId): array
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'moduleId',
                    value: $moduleId,
                    operator: OperationType::Equals
                ));
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'active',
                    value: 1,
                    operator: OperationType::Equals
                )
            );

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::modules_action,
                where: $where,
                select_fields: ['actionId', 'name']
            );
            return ModuleActionDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<string> */
    public static function getByPlatform(string $platform): array
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'value',
                    value: $platform,
                    operator: OperationType::Equals
                ));
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'typeId',
                    value: 'platform',
                    operator: OperationType::Equals
                )
            );
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'exclude',
                    value: 0,
                    operator: OperationType::Equals
                )
            );

            return DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::modules_access,
                where: $where,
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
     * Получение всего списка сравнения доступности модулей
     * @return array<ModuleAccessCompareDTO>
     */
    public static function getListCompare(?string $moduleId = null): array
    {
        try {
            if (!is_null($moduleId)) {
                $where = new Where(
                    new CompareOperator(
                        field: 'moduleId',
                        value: $moduleId,
                        operator: OperationType::Equals
                    ));
            }
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::modules_access_compare,
                where: $where ?? null
            );
            return ModuleAccessCompareDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<ModuleAllActionDTO> */
    // TODO Поменять название на getActions вместе с возвращаемой моделью
    public static function getModuleActions(): array
    {
        try {
            $dbName = DataBase::instance()->getDataBaseName(DataBaseType::main);

            $requestData = DataBase::instance()->execute(
                query: "SELECT modules.id as moduleId, modules.name as moduleName, action.active,
                    actionId, type, action.name, groupId, actionGroup.name as groupName FROM $dbName.modules
                    LEFT Join $dbName.modules_action as action On modules.id = action.moduleId
                    LEFT Join $dbName.modules_action_group as actionGroup On action.groupId = actionGroup.id
",
            );
            $data = $requestData->fetchAll();
            return ModuleAllActionDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }
}