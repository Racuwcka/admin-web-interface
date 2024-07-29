<?php

namespace api\Controllers;

use api\Core\Enums\MessageType;
use api\Core\Models\Result;
use api\Core\Storage\SessionStorage;
use api\Services\ModuleService;
use api\Services\SessionService;
use api\Services\WarehouseService;
use Core\Localizations\Localizations;

class WarehouseController
{
    public static function set(string $id): Result
    {
        if (!WarehouseService::isGroupCurrentWarehouseUser($id)) {
            return Result::error('warehouse.select.not_access');
        }

        $warehouse = WarehouseService::get($id);
        if (!$warehouse) {
            return Result::error('warehouse.select.not_identified');
        }

        if (SessionStorage::warehouseIdOrNull() == $warehouse->id) {
            return Result::error(
                message: 'warehouse.select.same_choice',
                messageType: MessageType::warning
            );
        }

        if (!SessionService::setData($warehouse)) {
            return Result::error('warehouse.select.update_error');
        }

        return Result::success(
            data: [
                "warehouse" => $warehouse,
                "accessModules" => ModuleService::getListAccessModules(
                    warehouseId: SessionStorage::warehouse()->id,
                    roleId: SessionStorage::user()->role
                )
            ],
            message: Localizations::get('warehouse.select.success') . $warehouse->name
        );
    }

    public static function getList(?bool $consolidated = null): Result
    {
        return Result::success(WarehouseService::getList($consolidated));
    }

    public static function getAccessList(): Result
    {
        $warehouseAccessList = WarehouseService::getAccessList();
        if (empty($warehouseAccessList)) {
            return Result::error('warehouse.not.have.subsidiary');
        }

        return Result::success($warehouseAccessList);
    }

    public static function search(string $name): Result
    {
        return Result::success(WarehouseService::search($name));
    }

    public static function generate(string $id): Result
    {
        if (!WarehouseService::generate($id)) {
            return Result::error('failed.generate.warehouse');
        }
        return Result::success();
    }
}
