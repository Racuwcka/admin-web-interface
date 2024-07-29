<?php

namespace api\Core\Repositories\LogError;

use api\Core\Classes\DataBase;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;

class LogErrorRepository
{
    public static function insert(
        string $data,
        string $trace,
        ?string $userId,
    ): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::mdm,
                table: DataBaseTable::log_error,
                values: [
                    "data" => $data,
                    "trace" => $trace,
                    "userId" => $userId
                ]);

            return true;
        }
        catch (\Throwable $e) {
            return false;
        }
    }
}