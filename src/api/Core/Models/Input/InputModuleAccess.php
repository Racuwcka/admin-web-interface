<?php

namespace api\Core\Models\Input;

class InputModuleAccess
{
    public function __construct(
        public string $moduleId,
        public string $typeId,
        public bool   $exclude,
        public mixed  $value
    ) {}

    /** @return array<self> */
    public static function fromJsonList(string $moduleId, string $json): array
    {
        try {
            $data = json_decode($json);
            foreach ($data->access as $item) {
                if (isset($item->exclude)) {
                    foreach ($item->exclude as $value) {
                        $list[] = new self(
                            moduleId: $moduleId,
                            typeId: $item->typeId,
                            exclude: true,
                            value: $value);
                    }
                }

                if (isset($item->value)) {
                    foreach ($item->value as $value) {
                        $list[] = new self(
                            moduleId: $moduleId,
                            typeId: $item->typeId,
                            exclude: false,
                            value: $value);
                    }
                }
            }
            return $list ?? [];
        }
        catch (\Throwable) {
            return [];
        }
    }

    public function toArray(): array
    {
        try {
            return [
                'moduleId' => $this->moduleId,
                'typeId' => $this->typeId,
                'exclude' => intval($this->exclude),
                'value' => is_bool($this->value) ? intval($this->value) : $this->value
            ];
        }
        catch (\Exception $e) {
            return [];
        }
    }

    public static function toList(array $list): array
    {
        try {
            return array_map(fn($item) => $item->toArray(), $list);
        }
        catch (\Throwable) {
            return [];
        }
    }
}