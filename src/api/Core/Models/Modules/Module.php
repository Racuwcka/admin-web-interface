<?php

namespace api\Core\Models\Modules;

use api\Core\Repositories\Module\DTO\ModuleDTO;

class Module
{
    public function __construct(
        public string $id,
        public string $labelUrl,
        public string $name,
        public bool $active
    ) {}

    /**
     * @param array<ModuleDTO> $list
     * @return array<self>
     */
    public static function fromListDTO(array $list): array
    {
        try {
            return array_map(fn($item) => new self(
                id: $item->id,
                labelUrl: self::fromCamelCase($item->id),
                name: $item->name,
                active: boolval($item->active)), $list);
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function fromCamelCase($input): string
    {
        $output = preg_replace_callback('/([A-Z])/', function($match) {
            return '-' . strtolower($match[1]);
        }, $input);

        if (str_starts_with($output, '-')) {
            $output = substr($output, 1);
        }

        return $output;
    }
}