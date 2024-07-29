<?php

namespace api\Services;

use api\Core\Classes\HandledError;
use api\Core\Mappers\DTO\SessionDataItemDTOMapper;
use api\Core\Models\Session\Session;
use api\Core\Models\Warehouse\Warehouse;
use api\Core\Repositories\Session\SessionRepository;
use api\Core\Storage\SessionStorage;
use api\Core\Storage\VariableStorage;

class SessionService
{
    /**
     * Создание сессии и сохранение в хранилище
     * @param int $userId
     * @param ?Warehouse $warehouse
     * @return ?string
     */
    public static function create(int $userId, ?Warehouse $warehouse): ?string
    {
        try {
            $token = self::generateToken(userId: $userId);
            $sessionId = md5($token);

            // Проверка ограничения от создания нескольких сессий при быстрых запросах к этому методу
            if (!is_null(SessionRepository::get(token: $token, platform: 'mdm'))) {
                return $token;
            }

            $data = SessionDataItemDTOMapper::toList(
                sessionId: $sessionId,
                warehouse: $warehouse?->id
            );

            if (!SessionRepository::create(
                session_id: $sessionId,
                user_id: $userId,
                platform: 'mdm',
                last_activity: time(),
                data: $data
            )) {
                return null;
            }

        if (!SessionService::getSession($token)) {
            return null;
        }

            return $token;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Обновляет данные сессии, сохраняет в хранилище
     * @param ?Warehouse $warehouse
     * @return bool
     */
    public static function setData(?Warehouse $warehouse): bool
    {
        $data = SessionDataItemDTOMapper::toList(
            sessionId: SessionStorage::id(),
            warehouse: $warehouse->id
        );

        if (!SessionRepository::setData($data)) {
            return false;
        }

        SessionStorage::setWarehouse($warehouse);

        return true;
    }

    public static function getSession(?string $token = null): bool
    {
        try {
            $sessionDTO = SessionRepository::get(token: $token ?? VariableStorage::$token, platform: "mdm");
            if(is_null($sessionDTO)) {
                throw new HandledError('errorGetSession');
            }

            $user = AccountService::get($sessionDTO->user_id);

            $sessionDataDTO = SessionRepository::getData(sessionId: $sessionDTO->session_id);
            $sessionDataList = array_column($sessionDataDTO, 'value', 'attr');

            $sessionWarehouse = null;

            if (isset($sessionDataList['warehouse'])) {
                $sessionWarehouse = WarehouseService::get($sessionDataList['warehouse']);
                if (is_null($sessionWarehouse)) {
                    throw new \Error('errorGetSessionWarehouse');
                }
            }

            SessionRepository::updateLastActivity(sessionId: $sessionDTO->session_id);

            $session = new Session(
                id: $sessionDTO->session_id,
                user: $user,
                warehouse: $sessionWarehouse
            );

            SessionStorage::set($session);
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function deleteSession(): bool
    {
        if (SessionRepository::delete(sessionId: SessionStorage::id(), platform: "mdm")) {
            SessionStorage::set(null);
            return true;
        }

        return false;
    }

    private static function generateToken(int $userId): string
    {
        return md5('keyTsd::121122' . $userId) . time();
    }
}