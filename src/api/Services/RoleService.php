<?php

namespace api\Services;

use api\Core\Models\Input\InputRole;
use api\Core\Models\Role\Role;
use api\Core\Repositories\Role\RoleRepository;

class RoleService
{
    /**
     * Получение списка ролей (с параметром, выбранных по полю "name")
     * @return array<Role>
     */
    public static function getList(?string $query): array
    {
        $roleDTO = RoleRepository::get($query);
        return Role::fromListDTO($roleDTO);
    }

    /**
     * Обновление модулей и действий выбранной роли
     * @param int $roleId
     * @param array<InputRole> $items
     * @return bool
     */
    public static function update(int $roleId, array $items): bool
    {
        try {
            foreach ($items as $item) {

                if ($item->check === false) {
                    $modulesDelete[] = $item->moduleId;
                } else {
                    $modulesUpdate[] = [
                        'roleId' => $roleId,
                        'moduleId'  => $item->moduleId,
                        'hash' => md5($roleId . $item->moduleId)
                    ];

                    foreach ($item->actions as $actionId => $check) {
                        if ($check === false) {
                            $actionsDelete[] = md5($roleId . $item->moduleId . $actionId);
                        } else {
                            $actionsUpdate[] = [
                                'roleId' => $roleId,
                                'moduleId' => $item->moduleId,
                                'actionId' => $actionId,
                                'hash' => md5($roleId . $item->moduleId . $actionId)
                            ];
                        }
                    }
                }
            }

            if (isset($modulesDelete)) {
                if (!RoleRepository::deleteModules(
                    roleId: $roleId,
                    moduleIds: array_unique($modulesDelete))) {
                    return false;
                }
            }

            if (isset($modulesUpdate)) {
                if (!RoleRepository::upsertModules($modulesUpdate)) {
                    return false;
                }
            }

            if (isset($actionsDelete)) {
                if (!RoleRepository::deleteActions($actionsDelete)) {
                    return false;
                }
            }

            if (isset($actionsUpdate)) {
                if (!RoleRepository::upsertActions($actionsUpdate)) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /** Создание роли */
    public static function create(string $name, string $description): bool
    {
        return RoleRepository::createRole(
            name: ucfirst(strtolower($name)),
            description: ucfirst(strtolower($description))
        );
    }

    /** Получение роли по id */
    public static function get(int $id): ?Role
    {
        $roleDTO = RoleRepository::getById($id);
        if (is_null($roleDTO)) {
            return null;
        }
        return Role::fromDTO($roleDTO);
    }

    public static function getName(string $id): ?string
    {
        return RoleRepository::getName($id);
    }

    public static function getByName(string $name): ?Role
    {
        $roleDTO = RoleRepository::getByName(ucfirst(strtolower($name)));
        if (is_null($roleDTO)) {
            return null;
        }
        return Role::fromDTO($roleDTO);
    }


    /** Удаление выбранной роли */
    public static function delete(int $id): bool
    {
        return RoleRepository::delete($id);
    }

    /**
     * Получение модулей для выбранной Роли
     * @return array<string>
     */
    public static function getModules(int $roleId): array
    {
        return RoleRepository::getRoleModules($roleId);
    }

    /**
     * Получение действий для выбранной Роли
     * @param int $roleId
     * @return array<string, array<string>>
     */
    public static function getActions(int $roleId): array
    {
        $moduleActionsDTO = RoleRepository::getActiveActions($roleId, []);

        foreach ($moduleActionsDTO as $item) {
            $moduleActions[$item->moduleId][] = $item->actionId;
        }

        return $moduleActions ?? [];
    }
}