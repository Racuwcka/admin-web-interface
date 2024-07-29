<?php

namespace api\Core\Storage;

use api\Core\Classes\ApiResponse;
use api\Core\Models\Session\Session;
use api\Core\Models\User;
use api\Core\Models\Warehouse\Warehouse;
use api\Core\Models\WorkArea\WorkArea;

class SessionStorage
{
    private static Session|null $session = null;

    public static function set(?Session $session): void
    {
        self::$session = $session;
    }

    public static function setWarehouse(?Warehouse $warehouse): void
    {
        if (is_null(self::$session)) {
            ApiResponse::error("missingCurrentSession");
        }

        self::$session->warehouse = $warehouse;
    }

    public static function has(): bool
    {
        return self::$session !== null;
    }

    public static function hasWarehouse(): bool
    {
        return self::$session?->warehouse !== null;
    }

    public static function id(): string
    {
        if (is_null(self::$session)) {
            ApiResponse::error("notInfoSession");
        }

        return self::$session->id;
    }

    public static function warehouse(): Warehouse {
        $warehouse = self::$session?->warehouse;
        if (is_null($warehouse)) {
            ApiResponse::error("notInfoSessionWarehouse");
        }

        return $warehouse;
    }

    public static function user(): User
    {
        $user = self::$session?->user;
        if (is_null($user)) {
            ApiResponse::error("notInfoSessionUser");
        }

        return $user;
    }

    public static function userIdOrNull(): ?int
    {
        return self::$session?->user->id;
    }

    public static function warehouseIdOrNull(): ?string
    {
        return self::$session?->warehouse?->id;
    }
}