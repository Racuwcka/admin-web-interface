<?php

namespace api\Controllers;

use api\Core\Enums\MessageType;
use api\Core\Models\Input\InputRole;
use api\Core\Models\Message;
use api\Core\Models\Result;
use api\Services\RoleService;

class RoleController {

    public static function get(int $roleId): Result
    {
        $moduleIds = RoleService::get($roleId);
        if (is_null($moduleIds)) {
            return Result::error('role.not.found');
        }
        return Result::do(true, $moduleIds);
    }

    public static function getList(string $query = null): Result
    {
        $roles = RoleService::getList($query);
        return Result::do(true, $roles ?: []);
    }

    public static function create(string $name, string $description): Result
    {
        if (!RoleService::create(name: $name, description: $description)) {
            return Result::error('failed.create.role');
        }

        $role = RoleService::getByName($name);
        if (is_null($role)) {
            return Result::error('failed.info.role');
        }

        return Result::do(true, $role);
    }

    public static function update(int $id, string $items): Result
    {
        $inputRoleList = InputRole::fromJsonList($items);
        if (count($inputRoleList) < 1) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: 'invalid.role.parameters')
            );
        }

        $result = RoleService::update(
            roleId: $id,
            items: $inputRoleList
        );
        return Result::do($result, $result);
    }

    public static function delete(int $id): Result
    {
        $result = RoleService::delete($id);
        if (!$result) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: 'failed.delete.role')
            );
        }
        return Result::do(true);
    }
}
