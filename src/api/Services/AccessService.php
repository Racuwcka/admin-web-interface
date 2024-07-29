<?php

namespace api\Services;

use api\Core\Enums\PlatformType;
use api\Core\Models\Access\AccessTypesDescript;
use api\Core\Models\AccessCompare;
use api\Core\Models\Modules\ModuleAccessRule;
use api\Core\Repositories\Access\AccessRepository;
use api\Core\Storage\SessionStorage;

class AccessService
{
    /**
     * @param array<int | string> $ids - список идентификаторов
     * @param array<string, array<ModuleAccessRule>> $ruleIds
     * @param array<string, array<array<AccessCompare>>> $compares
     * @param ?string $warehouseId
     * @return array<int | string> - список валидных идентификаторов
     */
    public static function get(
        array $ids,
        array $ruleIds,
        array $compares,
        ?string $warehouseId = null): array
    {
        try {
            $accessIds = [];

            $accessTypesDTO = AccessRepository::getAccessTypes();
            $accessTypes = array_column($accessTypesDTO, 'typeValue', 'typeId');

            $warehouse = WarehouseService::get(id: $warehouseId);
            if (is_null($warehouse)) {
                return [];
            }

            foreach ($ids as $id) {
                $rulesList = [];

                /** @var array<string, array<ModuleAccessRule>> $rulesList */
                if (isset($ruleIds[$id])) {
                    foreach ($ruleIds[$id] as $item) {
                        $rulesList[$item->type][] = $item;
                    }
                } else {
                    $result[$id] = true;
                }

                foreach ($rulesList as $type => $rules) {
                    $accessTypeValue = $accessTypes[$type] ?? null;
                    $access = false;

                    if (is_null($accessTypeValue)) {
                        continue;
                    }

                    foreach ($rules as $rule) {

                        $sourceAccessValue = $accessTypeValue == "bool" ? $rule->value == 1 : $rule->value;
                        $compareWithValue = match ($rule->type) {
                            'region' => $warehouse->region,
                            'warehouse' => $warehouse->id,
                            'user' => SessionStorage::user()->id,
                            'address' => $warehouse->address,
                            'retail' => $warehouse->retail,
                            'reciept' => $warehouse->reciept,
                            'support_package' => $warehouse->supportPackage,
                            'storage' => $warehouse->storage,
                            'platform' => PlatformType::mdm->value,
                            default => null
                        };

                        if (!is_null($sourceAccessValue) && !is_null($compareWithValue)) {
                            if ($rule->exclude) {
                                $access = $sourceAccessValue != $compareWithValue;
                                if (!$access) {
                                    break;
                                }
                            } else {
                                $access = $sourceAccessValue == $compareWithValue;
                                if ($access) {
                                    break;
                                }
                            }
                        }
                    }
                    $accessIds[$id][$type] = $access;
                }
            }

            foreach ($accessIds as $module => $items) { // перебор модулей
                if (isset($compares[$module])) { // проверка на существование правил сравнения для модуля
                    foreach ($compares[$module] as $compare) { // перебор правил сравнения модуля
                        foreach ($compare as $typeIds) {
                            // нахождение правил сравнения в правилах модуля
                            $diff = array_diff($typeIds->toArray(), array_keys($items));

                            if (empty($diff)) {
                                $items[$typeIds->type1] = $items[$typeIds->type2] = $items[$typeIds->type1] || $items[$typeIds->type2];
                            }
                        }
                    }
                }

                foreach ($items as $item) {
                    $result[$module] = ($result[$module] ?? true) && $item;
                }
            }
            return array_keys(array_filter($result ?? []));
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<AccessTypesDescript> */
    public static function getListType(): array
    {
        $accessTypesDTO = AccessRepository::getTypesDescript();
        return AccessTypesDescript::fromListDTO($accessTypesDTO);
    }
}