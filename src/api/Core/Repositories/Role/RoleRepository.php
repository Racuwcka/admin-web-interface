<?php

namespace api\Core\Repositories\Role;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Role\DTO\RoleActionsDTO;
use api\Core\Repositories\Role\DTO\RoleDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorEntryType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\EntryOperator;
use Database\Core\Models\Operators\LikeOperator;

class RoleRepository
{
    /** @return array<string> */
    public static function getRoleModules(int $roleId): array
    {
        try {
            return DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::role_modules,
                where: new Where(
                    operator: new CompareOperator(
                        field: 'roleId',
                        value: $roleId,
                        operator: OperationType::Equals
                    )
                ),
                select_fields: ['moduleId'],
                fetchColumn: true
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<RoleActionsDTO> */
    public static function getActiveActions(int $roleId, array $modules): array
    {
        try {
            $dbName = DataBase::instance()->getDataBaseName(DataBaseType::main);

            $arrQuestion = array_fill(0, count($modules), '?');
            $question = $arrQuestion ? "AND roleAction.moduleId IN" . '(' . join(",", $arrQuestion) . ')' : '';

            $requestData = DataBase::instance()->execute(
                query: "SELECT roleAction.`moduleId`, roleAction.`actionId` FROM $dbName.role_actions as roleAction
                        LEFT Join $dbName.modules_action as moduleAction On moduleAction.actionId = roleAction.actionId
                        WHERE roleAction.roleId = ? $question AND moduleAction.active = 1",
                args: [$roleId, ...$modules]
            );
            $data = $requestData->fetchAll();
            return RoleActionsDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<RoleDTO> */
    public static function get(?string $name): array
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'active',
                    value: 1,
                    operator: OperationType::Equals)
            );


            if (!is_null($name)) {
                $where->add(
                    logisticOperatorType: OperatorLogisticType::And,
                    operator: new LikeOperator(
                        field: 'name',
                        value: "%$name%")
                );
            }

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::role,
                where: $where
            );
            return RoleDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function getName(string $id): ?string
    {
        return self::getField(id: $id, field: 'name');
    }

    public static function getByName(string $name): ?RoleDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::role,
                where: new Where(
                    new CompareOperator(
                        field: 'name',
                        value: $name,
                        operator: OperationType::Equals)
                )
            );
            return RoleDTO::fromArray($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    public static function getById(int $id): ?RoleDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::role,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals))
            );
            return is_null($data) ? $data : RoleDTO::fromArray($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    public static function createRole(string $name, string $description): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::main,
                table: DataBaseTable::role,
                values: ['name' => $name, 'description' => $description]
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function createModules(array $items): bool
    {
        try {
            DataBase::instance()->insertMultiple(
                type: DataBaseType::main,
                table: DataBaseTable::role_modules,
                values: $items
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function createActions(array $items): bool
    {
        try {
            DataBase::instance()->insertMultiple(
                type: DataBaseType::main,
                table: DataBaseTable::role_actions,
                values: $items
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function upsertModules(array $items): bool
    {
        try {
            DataBase::instance()->upsert(
                type: DataBaseType::main,
                table: DataBaseTable::role_modules,
                values: $items
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function upsertActions(array $items): bool
    {
        try {
            DataBase::instance()->upsert(
                type: DataBaseType::main,
                table: DataBaseTable::role_actions,
                values: $items
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function deleteModules(int $roleId, array $moduleIds): bool
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'roleId',
                    value: $roleId,
                    operator: OperationType::Equals)
            );

            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new EntryOperator(
                    field: 'moduleId',
                    value: $moduleIds,
                    type: OperatorEntryType::In)
            );

            DataBase::instance()->delete(
                    type: DataBaseType::main,
                    table: DataBaseTable::role_modules,
                    where: $where) &&
            DataBase::instance()->delete(
                    type: DataBaseType::main,
                    table: DataBaseTable::role_actions,
                    where: $where
            );

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
    public static function deleteActions(array $items): bool
    {
        try {
            if (empty($items)) {
                return false;
            }

            $whereFirst = array_shift($items);
            $condition = new Where(
                new CompareOperator(
                    field: 'hash',
                    value: $whereFirst,
                    operator: OperationType::Equals)
            );

            foreach ($items as $value) {
                $condition->add(
                    logisticOperatorType: OperatorLogisticType::Or,
                    operator: new CompareOperator(
                        field: 'hash',
                        value: $value,
                        operator: OperationType::Equals
                    ));
            }

            DataBase::instance()->delete(
                type: DataBaseType::main,
                table: DataBaseTable::role_actions,
                where: $condition
            );

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function delete(int $id): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::main,
                table: DataBaseTable::role,
                values: ['active' => 0],
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

    public static function exists(int $id): bool
    {
        try {
            $count = DataBase::instance()->count(
                type: DataBaseType::main,
                table: DataBaseTable::role,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals))
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
            return DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::role,
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    )),
                select_fields: [$field],
                fetchColumn: true
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}