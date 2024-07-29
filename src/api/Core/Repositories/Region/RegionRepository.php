<?php

namespace api\Core\Repositories\Region;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Region\DTO\RegionDTO;
use api\Services\ThrowableLogger;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;

class RegionRepository
{
    /** @return array<RegionDTO> */
    public static function getList(): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::region
            );
            return RegionDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }
}