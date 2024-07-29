<?php

namespace Core\Services;

class EnvService
{
    private static array $cache = [];
    public static function get(string $path, array $keys = []): array|false
    {
        if (isset(self::$cache[$path])) {
            return self::$cache[$path];
        }

        $list = parse_ini_file($path, true);
        if (!$list) return false;

        if (count($keys) > 0) {
            foreach ($keys as $key) {
                if (!in_array($key, array_keys($list))) {
                    return false;
                }
            }
        }

        self::$cache[$path] = $list;

        return $list;
    }
}