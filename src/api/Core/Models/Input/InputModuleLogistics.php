<?php

namespace api\Core\Models\Input;

class InputModuleLogistics
{
    public function __construct(
        public string $articul,
        public string $size
    ) {}

    /** @return array<self> */
    public static function fromJsonList(string $json): array
    {
        try {
            $data = json_decode($json);
            foreach ($data as $item) {
                $list[] = new self(
                    articul: $item->articul,
                    size: $item->size
                );
            }
            return $list ?? [];
        }
        catch (\Throwable) {
            return [];
        }
    }
}