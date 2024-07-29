<?php

namespace Core\Services;

class WhiteList
{
    public static function checkDomain(string $domain): bool
    {
        $domains = EnvService::get(path: CONFIG_SRC . '/.env.domains');
        if (!$domains) {
            return false;
        }

        return self::check($domains, $domain);
    }
    public static function check(array $accessValue, string $value): bool
    {
        return in_array($value, $accessValue);
    }
}