<?php

namespace api\Core\Repositories\Integration;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Integration\DTO\IntegrationApiDTO;
use api\Core\Repositories\Integration\DTO\IntegrationResourceDataDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class IntegrationRepository
{
    /**
     * Получение всех ресурсов для текущего региона
     * @param ?string $region идентификатор региона
     * @return array<IntegrationResourceDataDTO>
     */
    public static function getList(?string $region): array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::main);

            $tableResource = DataBaseTable::integration_resource->value;
            $tableResourceData = DataBaseTable::integration_resource_data->value;
            $tableResourceRegion = DataBaseTable::integration_resource_region->value;

            $request = DataBase::instance()->execute(
                query: "SELECT resource.platform, data.* 
                       FROM $dataBaseName.$tableResourceData as data
                       LEFT JOIN $dataBaseName.$tableResourceRegion as region ON region.resource = data.resource
                       LEFT JOIN $dataBaseName.$tableResource as resource ON resource.resource = data.resource
                       WHERE region.region = ? OR region.region IS NULL",
                args: [$region]);

            $data = $request->fetchAll();
            return IntegrationResourceDataDTO::fromArrayToList($data);

        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function getIntegrationResourceData(string $region): ?IntegrationApiDTO
    {
        $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::main);
        $tableResourceRegion = DataBaseTable::integration_resource_region->value;
        $tableResourceData = DataBaseTable::integration_resource_data->value;
        try {
            $requestData = DataBase::instance()->execute(
                query: "SELECT data.*
                       FROM $dataBaseName.$tableResourceRegion as region
                       LEFT JOIN $dataBaseName.$tableResourceData as data ON region.resource = data.resource
                       WHERE region.region = ?",
                args: [$region]);
            $data = $requestData->fetch();
            return $data ? IntegrationApiDTO::fromArray($data) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getLogisticsResourceData(): ?IntegrationApiDTO
    {
        $data = DataBase::instance()->selectOne(
            type: DataBaseType::main,
            table: DataBaseTable::integration_resource_data,
            where: new Where(
                new CompareOperator(
                    field: 'resource',
                    value: 'logistics',
                    operator: OperationType::Equals
                ))
        );
        return $data ? IntegrationApiDTO::fromArray($data) : null;
    }
}