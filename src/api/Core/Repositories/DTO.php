<?php

namespace api\Core\Repositories;

abstract class DTO
{
    public function __construct(protected ?string $increment = null) {}

    public static function fromArray(array $data): ?static
    {
        try {
            $params = get_class_vars(get_called_class());
            unset($params['increment']);

            foreach ($params as $key => $item) {
                if (array_key_exists($key, $data)) {
                    $params[$key] = $data[$key];
                } else {
                    return null;
                }
            }
            return new static(...$params);
        } catch (\Exception $e) {
            return null;
        }
    }

    /** @return array<static> */
    public static function fromArrayToList(array $data): array
    {
        try {
            foreach ($data as $item) {
                $itemDTO = self::fromArray($item);
                if (!is_null($itemDTO)) {
                    $list[] = $itemDTO;
                }
            }

            return $list ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function toArray(): array
    {
        $params = get_class_vars(get_called_class());
        unset($params['increment']);

        foreach (array_keys($params) as $item) {
            if ($item !== $this->increment) {
                $result[$item] = $this->$item;
            }
        }
        return $result ?? [];
    }

    public static function toList(array $list): array
    {
        try {
            return array_map(fn($item) => $item->toArray(), $list);
        }
        catch (\Exception $e) {
            return [];
        }
    }
}