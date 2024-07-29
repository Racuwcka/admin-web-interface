<?php

namespace api\Services;

use api\Core\Classes\DataBase;
use api\Core\Enums\PlatformType;
use api\Core\Enums\Role\ActionType;
use api\Core\Models\AccessCompare;
use api\Core\Models\Input\InputModuleAccess;
use api\Core\Models\Modules\Module;
use api\Core\Models\Modules\ModuleAccess;
use api\Core\Models\Modules\ModuleAccessRule;
use api\Core\Models\Region\Region;
use api\Core\Repositories\Access\AccessRepository;
use api\Core\Repositories\Module\ModuleRepository;
use api\Core\Repositories\Region\RegionRepository;
use Core\Localizations\Localizations;

class ModuleService
{
    public static function  getListAccessModules(?string $warehouseId, int $roleId): array
    {
        $moduleIds = ModuleRepository::getListId(true);

        $listAccessRuleDTO = ModuleRepository::getListAccess();
        $accessRules = ModuleAccessRule::fromListModuleAccessRuleDTO($listAccessRuleDTO);

        $listAccessCompareDTO = ModuleRepository::getListCompare();
        $compares = AccessCompare::fromListModuleAccessCompareDTO($listAccessCompareDTO);

        $modules = AccessService::get(
            ids: $moduleIds,
            ruleIds: $accessRules,
            compares: $compares,
            warehouseId: $warehouseId,
        );

        $roleModules = RoleService::getModules($roleId);
        $roleActions = RoleService::getActions($roleId);

        $accessModules = array_intersect($roleModules, $modules);

        foreach ($accessModules as $module) {
            $result[] = new ModuleAccess(
                module: $module,
                actions: $roleActions[$module] ?? []
            );
        }
        return $result ?? [];
    }

    /** @return array<Module> */
    public static function getList(?string $query): array
    {
        $moduleDTO = ModuleRepository::getList($query);
        return Module::fromListDTO($moduleDTO);
    }

    public static function getListModulesActions(int $roleId = null): array
    {
        $modules = ModuleRepository::getModuleActions();
        $modulesPlatform = ModuleRepository::getByPlatform(PlatformType::mdm->value);

        $roleModules = [];
        $roleActions = [];

        if (!is_null($roleId)) {
            $roleModules = RoleService::getModules($roleId);
            $roleActions = RoleService::getActions($roleId);
        }

        $result = [];
        foreach ($modules as $module) {
            if (!in_array($module->moduleId, $modulesPlatform)) {
                continue;
            }

            if (!isset($result[$module->moduleId])) {
                $result[$module->moduleId] = [
                    'moduleId' => $module->moduleId,
                    'moduleName' => $module->moduleName,
                    'check' => in_array($module->moduleId, $roleModules),
                ];

                foreach (ActionType::cases() as $actionType) {
                    $result[$module->moduleId]['types'][$actionType->value] = [
                        'ownerFields' => [],
                        'groupFields' => []
                    ];
                }
            }

            if (is_null($module->actionId)) { // у модуля нет действий
                continue;
            }

            $item = [
                'name' => $module->actionId,
                'type' => $module->type,
                'label' => Localizations::get($module->name),
                'disable' => $module->active === 0,
                'check' => isset($roleActions[$module->moduleId]) && in_array($module->actionId, $roleActions[$module->moduleId])
            ];

            if (!is_null($module->groupId)) {
                $result[$module->moduleId]['types'][$module->type]['groupFields'][$module->groupId][] = $item;
            } else {
                $result[$module->moduleId]['types'][$module->type]['ownerFields'][] = $item;
            }
        }

        $result = array_values($result);
        for ($i = 0; $i < count($result); $i++) {
            foreach (ActionType::cases() as $actionType) {
                $result[$i]['types'][$actionType->value]['groupFields'] =
                    array_values($result[$i]['types'][$actionType->value]['groupFields']);
            }

        }
        return $result;
    }

    public static function isset(string $moduleId): bool
    {
        return !is_null(ModuleRepository::isset($moduleId));
    }

    public static function getAccessModule(string $moduleId): ?array
    {
        $listAccessModuleDTO = ModuleRepository::getListAccess($moduleId);

        $listCompareDTO = ModuleRepository::getListCompare($moduleId);

        foreach ($listCompareDTO as $compareDTO) {
            $listCompare[$compareDTO->compareId][] = $compareDTO->typeId;
        }
        if (!empty($listCompare)) {
            $result['compare'] = array_values($listCompare);
        }

        $accessTypesDTO = AccessRepository::getAccessTypes();
        $accessTypes = array_column($accessTypesDTO, 'typeValue', 'typeId');

        foreach ($listAccessModuleDTO as $accessModuleDTO) {
            $value = $accessTypes[$accessModuleDTO->typeId] == "bool" ? $accessModuleDTO->value == 1 : $accessModuleDTO->value;
            if ($accessModuleDTO->exclude) {
                $result['access'][$accessModuleDTO->typeId]['exclude'][] = $value;
            } else {
                $result['access'][$accessModuleDTO->typeId]['value'][] = $value;
            }
        }
        return $result ?? [];
    }

    /** @param array<InputModuleAccess> $moduleAccess */
    public static function setAccessModule(string $moduleId, array $moduleAccess, array $moduleCompare): bool
    {
        try {
            DataBase::$instance->beginTransaction();
            if (!ModuleRepository::deleteAccess($moduleId)) {
                throw new \Exception();
            }
            if (!ModuleRepository::insertAccess($moduleAccess)) {
                throw new \Exception();
            }

            if (!ModuleRepository::deleteAccessCompare($moduleId)) {
                throw new \Exception();
            }

            if (!empty($moduleCompare)) {
                if (!ModuleRepository::insertAccessCompare($moduleCompare)) {
                    throw new \Exception();
                }
            }

            DataBase::$instance->commit();
            return true;
        } catch (\Throwable) {
            DataBase::$instance->rollBack();
            return false;
        }
    }

    /** Получение локализованного массива регионов */
    public static function getLocalizationListRegion(): array
    {
        $listRegionDTO = RegionRepository::getList();
        foreach ($listRegionDTO as $region) {
            $result[] = [
                'id' => $region->id,
                'name' => Localizations::get($region->name)
            ];
        }
        return $result ?? [];
    }

    /** @return array<Region> */
    public static function getListRegion(): array
    {
        $listRegionDTO = RegionRepository::getList();
        return Region::fromListDTO($listRegionDTO);
    }
}