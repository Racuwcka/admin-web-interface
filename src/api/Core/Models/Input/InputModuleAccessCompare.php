<?php

namespace api\Core\Models\Input;

class InputModuleAccessCompare
{
    public function __construct(
        public string $moduleId,
        public array $typeIds
    ) {}

    /** @return ?array<self> */
    public static function fromJsonList(string $moduleId, string $json): ?array
    {
        try {
            $data = json_decode($json);
            foreach ($data->compare as $item) {
                $list[] = new self(
                    moduleId: $moduleId,
                    typeIds: $item->typeId);
            }
            return $list ?? [];
        }
        catch (\Throwable) {
            return null;
        }
    }

    /** @param array<InputModuleAccessCompare> $list */
    public static function toList(array $list): array
    {
        try {
            foreach ($list as $item) {
                $hash = md5($item->moduleId.implode('', $item->typeIds));
                foreach ($item->typeIds as $typeId) {
                    $result[] = [
                        'moduleId' => $item->moduleId,
                        'typeId' => $typeId,
                        'compareId' => $hash
                    ];
                }
            }
            return $result ?? [];
        }
        catch (\Throwable) {
            return [];
        }
    }
}