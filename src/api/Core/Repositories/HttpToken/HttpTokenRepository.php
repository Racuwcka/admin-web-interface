<?php

namespace api\Core\Repositories\HttpToken;

use api\Core\Classes\DataBase;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class HttpTokenRepository
{
    public static function get(string $token): bool
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::http_token,
                where: new Where(
                    new CompareOperator(
                        field: 'token',
                        value: $token,
                        operator: OperationType::Equals
                    ))
            );

            return (bool)$data;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}