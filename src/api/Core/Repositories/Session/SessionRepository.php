<?php

namespace api\Core\Repositories\Session;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Session\DTO\SessionDataItemDTO;
use api\Core\Repositories\Session\DTO\SessionDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class SessionRepository
{
    /**
     * Получение сессий
     * @param string $token - Токен (Токен != SessionID)
     * @param string $platform
     * @return ?SessionDTO
     */
    public static function get(string $token, string $platform): ?SessionDTO
    {
        try {
            $where = new Where();
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'session_id',
                    value: md5($token),
                    operator: OperationType::Equals
                )
            );
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'platform',
                    value: $platform,
                    operator: OperationType::Equals
                )
            );

            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::sessions,
                where: $where
            );
            return $data ? SessionDTO::fromArray($data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Создание сессии
     * @param string $session_id
     * @param int $user_id
     * @param string $platform
     * @param int $last_activity
     * @param array<SessionDataItemDTO> $data
     * @return bool
     */
    public static function create(
        string $session_id,
        int $user_id,
        string $platform,
        int $last_activity,
        array $data = []
    ): bool
    {
        try {
            $dto = new SessionDTO(
                session_id: $session_id,
                user_id: $user_id,
                platform: $platform,
                last_activity: $last_activity
            );

            DataBase::instance()->beginTransaction();

            DataBase::instance()->insert(
                type: DataBaseType::main,
                table: DataBaseTable::sessions,
                values: $dto->toArray()
            );

            DataBase::instance()->insertMultiple(
                type: DataBaseType::main,
                table: DataBaseTable::sessions_data,
                values: SessionDataItemDTO::toList($data)
            );

            DataBase::instance()->commit();

            return true;
        } catch (\Throwable $e) {
            DataBase::instance()->rollBack();
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Получение свойств сессии
     * @param string $sessionId - идентификатор сессии
     * @return array<SessionDataItemDTO>
     */
    public static function getData(string $sessionId): array
    {
        try {
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::sessions_data,
                where: new Where(
                    new CompareOperator(
                        field: 'session_id',
                        value: $sessionId,
                        operator: OperationType::Equals))
            );
            return SessionDataItemDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Обновление/запись нескольких свойств сессии
     * @param array<SessionDataItemDTO> $items - массив свойств данных сессии
     * @return bool
     */
    public static function setData(array $items): bool
    {
        try {
            DataBase::instance()->upsert(
                type: DataBaseType::main,
                table: DataBaseTable::sessions_data,
                values: SessionDataItemDTO::toList($items)
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Обновление/запись одного свойства в сессии
     * @param string $sessionId - идентификатор сессий
     * @param string $attr - свойство
     * @param string|null $value - значение
     * @return bool
     */
    public static function setDataField(string $sessionId, string $attr, ?string $value): bool
    {
        try {
            DataBase::instance()->upsert(
                type: DataBaseType::main,
                table: DataBaseTable::sessions_data,
                values: [
                    new SessionDataItemDTO(
                        data_id: md5($sessionId . $attr),
                        session_id: $sessionId,
                        attr: $attr,
                        value: $value
                    )
                ]
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Обновление активности сессии
     * @param string $sessionId - идентификатор сессии
     * @return bool
     */
    public static function updateLastActivity(string $sessionId): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::main,
                table: DataBaseTable::sessions,
                values: ['last_activity' => time()],
                where: new Where(
                    new CompareOperator(
                        field: 'session_id',
                        value: $sessionId,
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
     * Удаление всех сессии пользователя с определенной платформой
     * @param string $platform - обозначение платформы
     * @param int $userId - идентификатор пользователя
     * @return bool
     */
    public static function deleteUserPlatform(string $platform, int $userId): bool
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'user_id',
                    value: $userId,
                    operator: OperationType::Equals
                ));
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'platform',
                    value: $platform,
                    operator: OperationType::Equals
                )
            );

            DataBase::instance()->delete(
                type: DataBaseType::main,
                table: DataBaseTable::sessions,
                where: $where
            );

            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Удаление всех сессии пользователя
     * @param int $userId - идентификатор пользователя
     * @return bool
     */
    public static function deleteUser(int $userId): bool
    {
        try {
            DataBase::instance()->delete(
                type: DataBaseType::main,
                table: DataBaseTable::sessions,
                where: new Where(
                    new CompareOperator(
                        field: 'user_id',
                        value: $userId,
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
     * Удаление сессии
     * @param string $sessionId идентификатор сессии
     * @param string $platform
     * @return bool
     */
    public static function delete(string $sessionId, string $platform): bool
    {
        try {
            $where = new Where();
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'session_id',
                    value: $sessionId,
                    operator: OperationType::Equals
                )
            );
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'platform',
                    value: $platform,
                    operator: OperationType::Equals
                )
            );

            DataBase::instance()->delete(
                type: DataBaseType::main,
                table: DataBaseTable::sessions,
                where: $where);

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}