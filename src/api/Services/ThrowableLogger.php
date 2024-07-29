<?php

namespace api\Services;

use api\Config;
use api\Core\Classes\DataBase;
use api\Core\Repositories\LogError\LogErrorRepository;
use api\Core\Storage\SessionStorage;

class ThrowableLogger
{
    public static function catch(\Throwable $e): void
    {
        if (!DataBase::isInstance()) {
            return;
        }

        LogErrorRepository::insert(
            data: $e->getMessage(),
            trace: $e->getTraceAsString(),
            userId: SessionStorage::userIdOrNull());
        if (Config::$debug) {
            var_dump($e);
        }
    }
}