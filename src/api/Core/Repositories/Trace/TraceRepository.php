<?php

namespace api\Core\Repositories\Trace;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Trace\DTO\TraceDTO;
use api\Services\ThrowableLogger;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;

class TraceRepository
{
    /** @return array<TraceDTO> */
    public static function getList(): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::data,
                table: DataBaseTable::trace,
                order_value: ['date' => 'DESC'],
                select_fields: ['terminalId', 'name']
            );

            return TraceDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }
}