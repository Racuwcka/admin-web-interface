<?php

namespace api\Core\Classes;

use api\Services\ThrowableLogger;
use Core\Services\EnvService;

class DataBase
{
    public static \Database\DataBase $instance;

    public static function setUp(bool $production): void
    {

        $config = EnvService::get(
            path: CONFIG_SRC . '/.env.db',
            keys: ['host', 'name', 'user', 'password']
        );

        if (!$config) {
            throw new HandledError('dbConfigurationIsMissing');
        }

        try {
            self::$instance = new \Database\DataBase(
                host: $config['host'],
                dbName: $config['name'],
                user: $config['user'],
                password: $config['password'],
                prod: $production
            );
        } catch (\Exception $e) {
            ThrowableLogger::catch($e);
            throw new HandledError('failedConnectDatabase');
        }
    }

    public static function instance(): \Database\DataBase
    {
        if (!isset(self::$instance)) {
            ApiResponse::error("dbIsNotConnected");
        }
        return self::$instance;
    }

    public static function isInstance(): bool
    {
        return isset(self::$instance);
    }
}