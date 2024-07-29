<?php

namespace api\Services;

use api\Core\Repositories\Device\DeviceRepository;

class DeviceService
{
    public static function getList(): array
    {
        return DeviceRepository::getList();
    }

    public static function getPercent(): array
    {
        try {
            $devicesPercent = DeviceRepository::getPercent();
            $countDevices = array_sum(array_column($devicesPercent,'devices'));

            foreach ($devicesPercent as $item) {
                $result[] = [
                    'appVersion' => $item->app_version,
                    'countDevices' => $item->devices,
                    'percent' => round($item->devices / ($countDevices / 100), 1)
                ];
            }

            return $result ?? [];
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function getLatestDataList(): array
    {
        $devices = DeviceRepository::getLatestDataList();

        foreach ($devices as $device) {
            $result[$device->appVersion][] = [
                'id' => $device->id,
                'warehouse' => $device->warehouseName,
                'user' => [
                    'name' => $device->userName,
                    'photo' => $device->photo
                ]
            ];
        }
        return $result ?? [];
    }

    public static function getHistory(string $id): array
    {
        $deviceHistory = DeviceRepository::getHistory($id);

        foreach ($deviceHistory as $item) {
            $result[] = [
                'warehouseName' => $item->warehouse_name,
                'userName' => $item->user_name,
                'appVersion' => $item->app_version,
                'date' => date('Y-m-d H:i:s', $item->date)
            ];
        }
        return $result ?? [];
    }
}