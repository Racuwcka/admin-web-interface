<?php

namespace api\Core\Repositories\Access;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Access\DTO\AccessTypeDescriptDTO;
use api\Core\Repositories\Access\DTO\AccessTypeValueDTO;
use api\Services\ThrowableLogger;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;

class AccessRepository
{
    /**
     * Получение доступных типов правил
     * @return array<AccessTypeValueDTO>
     */
    public static function getAccessTypes(): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::access_types,
                select_fields: ['typeId', 'typeValue']
            );
            return AccessTypeValueDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function getTypesDescript(): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::access_types,
                select_fields: ['typeId', 'descript']);

            return AccessTypeDescriptDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }
}