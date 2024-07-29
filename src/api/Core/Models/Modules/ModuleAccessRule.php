<?php

namespace api\Core\Models\Modules;

use api\Core\Repositories\Module\DTO\ModuleAccessRuleDTO;

class ModuleAccessRule
{
    public function __construct(
        public int    $id,
        public string $type,
        public string $value,
        public bool   $exclude,
    ) {}

    /**
     * @param array<ModuleAccessRuleDTO> $list
     * @return array<string, array<self>>
     */
    public static function fromListModuleAccessRuleDTO(array $list): array
    {
        try {
            foreach ($list as $item) {
                $result[$item->moduleId][] = new self(
                    id: $item->id,
                    type: $item->typeId,
                    value: $item->value,
                    exclude: $item->exclude == 1
                );
            }
            return $result ?? [];
        }
        catch (\Exception $e) {
            return [];
        }
    }
}