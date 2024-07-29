<?php

namespace api\Controllers;

use api\Core\Enums\MessageType;
use api\Core\Models\Input\InputModuleAccess;
use api\Core\Models\Input\InputModuleAccessCompare;
use api\Core\Models\Message;
use api\Core\Models\Result;
use api\Services\AccessService;
use api\Services\ModuleService;

class ModuleController {

    public static function getList(string $query = null): Result
    {
        $moduleIds = ModuleService::getList($query);
        return Result::do(true, $moduleIds);
    }

    public static function getListType(): Result
    {
        $accessTypes = AccessService::getListType();
        return Result::do(true, $accessTypes);
    }

    public static function getListActions(int $roleId = null): Result
    {
        $moduleIds = ModuleService::getListModulesActions($roleId);
        return Result::do(true, $moduleIds);
    }

    public static function getAccessModule(string $moduleId): Result
    {
        if (!ModuleService::isset($moduleId)) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: 'module.not.found')
            );
        }

        $accessModuleIds = ModuleService::getAccessModule($moduleId);
        return Result::do(true, $accessModuleIds);
    }

    public static function setAccessModule(string $moduleId, string $items): Result
    {
        $inputAccessList = InputModuleAccess::fromJsonList($moduleId, $items);
        if (count($inputAccessList) < 1) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: 'invalid.access.modules.parameters')
            );
        }

        $inputCompareList = InputModuleAccessCompare::fromJsonList($moduleId, $items);
        if (is_null($inputCompareList)) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: '') // TODO
            );
        }

        if (!ModuleService::setAccessModule(
            moduleId: $moduleId,
            moduleAccess: $inputAccessList,
            moduleCompare: $inputCompareList)
        ) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: '') // TODO
            );
        }
        return Result::do(true);
    }

    public static function getListRegion(): Result
    {
        return Result::do(true, ModuleService::getLocalizationListRegion());
    }
}
