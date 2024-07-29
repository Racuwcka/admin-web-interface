<?php

namespace api\Core\Repositories\Device;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Device\DTO\DeviceAppVersionDTO;
use api\Core\Repositories\Device\DTO\DeviceCurrentHistoryDTO;
use api\Core\Repositories\Device\DTO\DeviceDTO;
use api\Core\Repositories\Device\DTO\DeviceHistoryDTO;
use api\Core\Repositories\Device\DTO\DeviceLatestDataDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class DeviceRepository
{
    /**
     * Получение информации об устройстве
     * @param string $did идентификатор устройства
     * @return ?DeviceDTO
     */
    public static function get(string $did): ?DeviceDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::devices,
                where: new Where(
                    new CompareOperator(
                        field: 'did',
                        value: $did,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? DeviceDTO::fromArray($data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /** Получения списка устройств */
    public static function getList(): array
    {
        try {
            return DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::devices,
                order_value: ['id' => 'ASC'],
                select_fields: ['id']
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<DeviceAppVersionDTO> */
    public static function getPercent(): array
    {
        try {
            $dbName = DataBase::instance()->getDataBaseName(DataBaseType::main);
            $requestData = DataBase::instance()->execute(
                query: "SELECT app_version, COUNT(*) AS devices FROM $dbName.devices GROUP BY app_version"
            );
            $data = $requestData->fetchAll();
            return DeviceAppVersionDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** @return array<DeviceLatestDataDTO> */
    public static function getLatestDataList(): array
    {
        try {
            $dbName = DataBase::instance()->getDataBaseName(DataBaseType::main);
            $requestData = DataBase::instance()->execute(
                query: "SELECT devices.id, warehouse.name as warehouseName, devices.app_version as appVersion,
                        CONCAT_WS(' ', account.last_name, account.first_name, account.second_name) as userName,
                        account.photo FROM $dbName.devices
                        LEFT JOIN $dbName.warehouse ON devices.last_warehouse = warehouse.id
                        LEFT JOIN $dbName.account ON devices.last_user = account.id"
            );
            $data = $requestData->fetchAll();
            return DeviceLatestDataDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function is(string $did): bool
    {
        try {
            return (bool) DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::devices,
                where: new Where(
                    new CompareOperator(
                        field: 'did',
                        value: $did,
                        operator: OperationType::Equals
                    ))
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Открепить пользователя от устройства
     * @param string $did идентификатор устройства
     * @return bool
     */
    public static function deviceOffUser(string $did): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::main,
                table: DataBaseTable::devices,
                values: ['user' => ''],
                where: new Where(
                    new CompareOperator(
                        field: 'did',
                        value: $did,
                        operator: OperationType::Equals
                    ))
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Получение внутреннего идентификатора устройства
     * @param string $did идентификатор устройства
     * @return ?int
     */
    public static function getId(string $did): ?int
    {
        return self::getField(did: $did, field: 'id') ?: null;
    }

    /** Обновляем данные об устройстве */
    public static function upsert(
        string  $did,
        int     $app_version,
        ?string $warehouse,
        ?int    $user,
    ): bool
    {
        try {
            $device = [
                "app_version" => $app_version,
                "warehouse" => $warehouse,
                "user" => $user,
                "date" => time()
            ];

            if (self::is($did)) {
                if ($warehouse !== null) {
                    $device["last_warehouse"] = $warehouse;
                }

                if ($user !== null) {
                    $device["last_user"] = $user;
                }

                DataBase::instance()->update(
                    type: DataBaseType::main,
                    table: DataBaseTable::devices,
                    values: $device,
                    where: new Where(
                        new CompareOperator(
                            field: 'did',
                            value: $did,
                            operator: OperationType::Equals
                        )
                    ));
            } else {
                $device["did"] = $did;
                $device["last_warehouse"] = $warehouse;
                $device["last_user"] = $user;

                DataBase::instance()->insert(
                    type: DataBaseType::main,
                    table: DataBaseTable::devices,
                    values: $device);
            }

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Записать новое устройство
     * @param DeviceDTO $device
     * @return bool
     */
    public static function insert(DeviceDTO $device): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::main,
                table: DataBaseTable::devices,
                values: $device->toArray()
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Записать в историю устройства
     * @param DeviceHistoryDTO $deviceHistory
     * @return bool
     */
    public static function insertHistory(DeviceHistoryDTO $deviceHistory): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::main,
                table: DataBaseTable::devices_history,
                values: $deviceHistory->toArray()
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /** @return array<DeviceCurrentHistoryDTO> */
    public static function getHistory(string $id): array
    {
        try {
            $db = DataBase::instance()->getDataBaseName(DataBaseType::main);
            $requestData = DataBase::instance()->execute(
                query: "SELECT $db.warehouse.name as warehouse_name,
                        CONCAT_WS(' ', $db.account.last_name, $db.account.first_name, $db.account.second_name) as user_name,
                        $db.devices_history.app_version, $db.devices_history.date FROM $db.devices_history
                    
                        LEFT JOIN $db.devices ON $db.devices.did = $db.devices_history.did
                
                        LEFT JOIN $db.warehouse ON $db.warehouse.id = $db.devices_history.warehouse
                
                        LEFT JOIN $db.account ON $db.account.id = $db.devices_history.user
                                                                      
                        WHERE $db.devices.id = '$id'"
            );
            $data = $requestData->fetchAll();
            return DeviceCurrentHistoryDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    private static function getField(string $did, string $field): mixed
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::devices,
                where: new Where(
                    new CompareOperator(
                        field: 'did',
                        value: $did,
                        operator: OperationType::Equals
                    )),
                select_fields: [$field],
                fetchColumn: true
            );
            return $data ?: null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}