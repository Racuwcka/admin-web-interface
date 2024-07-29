<?php

namespace api\Core\Models;

use api\Core\Repositories\Module\DTO\ModuleAccessCompareDTO;

class AccessCompare
{
    public function __construct(
        public string $type1,
        public string $type2
    ) {}

    public function toArray(): array
    {
        try {
            return [
                $this->type1,
                $this->type2
            ];
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param array<ModuleAccessCompareDTO> $list
     * @return array<string, array<array<self>>>
     */
    public static function fromListModuleAccessCompareDTO(array $list): array {
        try {
            $compareList = [];

            foreach ($list as $item) {
                $compareList[$item->compareId]['moduleId'] = $item->moduleId;
                $compareList[$item->compareId]['typeIds'][] = $item->typeId;
            }

            foreach ($compareList as $key => $compare) {
                for ($i = 0; $i < count($compare['typeIds']); $i++) {
                    for ($j = $i + 1; $j < count($compare['typeIds']); $j++) {
                        $result[$compare['moduleId']][$key][] = new self(
                            type1: $compare['typeIds'][$i],
                            type2: $compare['typeIds'][$j]
                        );
                    }
                }
                if (isset($result[$compare['moduleId']])) {
                    $result[$compare['moduleId']] = array_values($result[$compare['moduleId']]);
                }
            }
            return $result ?? [];
        }
        catch (\Throwable) {
            return [];
        }
    }
}