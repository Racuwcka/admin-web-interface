<?php

namespace Migration\Core;

use Core\Services\EnvService;

class DataBase
{
    public static \Database\DataBase $instance;

    public static function setUp(): bool
    {
        try {
            $config = EnvService::get(
                path: CONFIG_SRC . '/.env.db',
                keys: ['host', 'name', 'user', 'password']
            );

            if (!$config) {
                return false;
            }

            self::$instance = new \Database\DataBase(
                host: $config['host'],
                dbName: $config['name'],
                user: $config['user'],
                password: $config['password'],
                prod: true
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}