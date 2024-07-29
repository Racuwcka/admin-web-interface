<?php

namespace api\Core\Repositories\Account;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Account\DTO\AccountDTO;
use api\Core\Repositories\Account\DTO\PersonalizationAppDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\BracketType;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorIsNullType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\IsNullOperator;
use Database\Core\Models\Operators\LikeOperator;

class AccountRepository {

    /**
     * Получить пользователя по идентификатору авторизации
     * @param string $authId идентификатор авторизации
     * @return ?AccountDTO
     */
    public static function getUserByAuthId(string $authId): ?AccountDTO
    {
        return self::getField(field: "auth_id", value: md5($authId));
    }

    /**
     * Получить пользователя по идентификатору
     * @param int $id идентификатор пользователя
     * @return AccountDTO|null
     */
    public static function getById(int $id): ?AccountDTO
    {
        return self::getField(field: "id", value: $id);
    }

    /**
     * Получить пользователя по идентификатор битрикса
     * @param int $bxId идентификатор битрикса
     * @return AccountDTO|null
     */
    public static function getByBxId(int $bxId): ?AccountDTO
    {
        return self::getField(field: "bx_id", value: $bxId);
    }

    /**
     * Получить персонализированные настройки
     * @param int $userId идентификатор пользователя
     * @return PersonalizationAppDTO|null
     */
    public static function getPersonalizationApp(int $userId): ?PersonalizationAppDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::personalization_app,
                where: new Where(
                    new CompareOperator(
                        field: 'user_id',
                        value: $userId,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? PersonalizationAppDTO::fromArray(data: $data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Обновить персонализированные настройки
     * @param PersonalizationAppDTO $data
     * @return bool
     */
    public static function updatePersonalizationApp(PersonalizationAppDTO $data): bool
    {
        try {
            DataBase::instance()->upsert(
                type: DataBaseType::main,
                table: DataBaseTable::personalization_app,
                values: [$data->toArray()]
            );

            return true;
        } catch (\Exception $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Получение полного имени пользователя
     * @param int $id идентификатор пользователя
     * @return string|null
     */
    public static function getName(int $id): ?string
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::main);
            $table = DataBaseTable::account->value;

            $requestData = DataBase::instance()->execute(
                query: "SELECT `first_name`, `last_name`, `second_name` FROM $dataBaseName.$table WHERE `id` = ?",
                args: [$id]
            );

            $data = $requestData->fetch();
            $name = trim(($data['last_name'] ?? '') . ' ' .
                ($data['first_name'] ?? '') . ' ' .
                ($data['second_name'] ?? ''));

            if (empty($name)) {
                return null;
            }

            return $name;
        } catch (\Exception $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /** Обновление данных пользователя, новыми данными из битркса */
    public static function updateBitrixData(
        int     $id,
        string  $bxToken,
        int     $bxId,
        ?string $firstName,
        ?string $lastName,
        ?string $secondName,
        ?string $photo,
        ?string $birthday,
        ?string $gender,
        string $platform
    ): bool
    {
        try {
            DataBase::instance()->beginTransaction();

            DataBase::instance()->update(
                type: DataBaseType::main,
                table: DataBaseTable::account,
                values: [
                    'bx_id' => $bxId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'second_name' => $secondName,
                    'photo' => $photo,
                    'birthday' => $birthday,
                    'gender' => $gender,
                ],
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    )
                ));

            DataBase::instance()->upsert(
                type: DataBaseType::main,
                table: DataBaseTable::bitrix_token,
                values: [
                    [
                        "hash" => md5($id . $platform),
                        "userId" => $id,
                        "platform" => $platform,
                        "token" => $bxToken
                    ]
                ]
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
     * Обновить склад у пользователя
     * @param int $id идентификатор пользователя
     * @param string|null $warehouse идентификатор склада
     * @return bool
     */
    public static function updateWarehouse(int $id, ?string $warehouse): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::main,
                table: DataBaseTable::account,
                values: ['warehouse' => $warehouse],
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals
                    )
                ));
            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /** Записываем нового пользователя */
    public static function create(AccountDTO $user, string $bxToken, string $platform): bool
    {
        try {
            DataBase::instance()->beginTransaction();

            DataBase::instance()->insert(
                type: DataBaseType::main,
                table: DataBaseTable::account,
                values: $user->toArray()
            );

            $userId = DataBase::instance()->lastInsertId();

            DataBase::instance()->upsert(
                type: DataBaseType::main,
                table: DataBaseTable::bitrix_token,
                values: [
                    [
                        "hash" => md5($userId . $platform),
                        "userId" => $userId,
                        "platform" => $platform,
                        "token" => $bxToken
                    ]
                ]
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
     * Получение списка пользователей
     * @return array<AccountDTO>
     */
    public static function getList(?int $limit, ?int $page): array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::main);
            $table = DataBaseTable::account->value;
            $query = "SELECT * FROM $dataBaseName.$table WHERE bx_id IS NOT NULL";

            if (!is_null($limit)) {
                $query .= " LIMIT ?";
                $args[] = $limit;

                if (!is_null($page)) {
                    $query .= " OFFSET ? ";
                    $args[] = $limit * ($page - 1);
                }
            }

            $requestData = DataBase::instance()->execute(
                query: $query,
                args: $args ?? []
            );
            $data = $requestData->fetchAll();

            return AccountDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** Получение количества пользователей */
    public static function getCount(): int
    {
        try {
            return DataBase::instance()->count(
                type: DataBaseType::main,
                table: DataBaseTable::account,
                where: new Where(
                    new IsNullOperator(
                        field: 'bx_id',
                        type: OperatorIsNullType::NotNull))
            );
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return 0;
        }
    }

    /** Обновление полей склада, роли, active у пользователя */
    public static function update(int $id, string $warehouse, int $role, int $active): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::main,
                table: DataBaseTable::account,
                values: [
                    "warehouse" => $warehouse,
                    "roleId" => $role,
                    "active" => $active
                ],
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals))
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Поиск пользователя по трем полям имени
     * @return array<AccountDTO>
     */
    public static function search(array $names): array
    {
        try {
            $name = array_shift($names);
            $where = (new Where(
                new LikeOperator(
                    field: 'first_name',
                    value: "%$name%",
                    bracket: BracketType::OpenBracket))
            )->add(
                logisticOperatorType: OperatorLogisticType::Or,
                operator: new LikeOperator(
                    field: 'last_name',
                    value: "%$name%")
            )->add(
                logisticOperatorType: OperatorLogisticType::Or,
                operator: new LikeOperator(
                    field: 'second_name',
                    value: "%$name%",
                    bracket: BracketType::CloseBracket),
            );

            if (!empty($names)) {
                $nameColumns = ['first_name', 'last_name', 'second_name'];

                foreach ($names as $value) {
                    for ($i = 0; $i < 3; $i++) {
                        if ($i == 0) {
                            $bracket = BracketType::OpenBracket;
                        } else if ($i == 2) {
                            $bracket = BracketType::CloseBracket;
                        } else {
                            $bracket = null;
                        }

                        $where->add(
                            logisticOperatorType: $i == 0 ? OperatorLogisticType::And : OperatorLogisticType::Or,
                            operator: new LikeOperator(
                                field: $nameColumns[$i],
                                value: "%$value%",
                                bracket: $bracket)
                        );
                    }

                }
            }
            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::account,
                where: $where
            );
            return AccountDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }


    /** Обновление поля auth_id у пользователя */
    public static function updateAuthId(string $authId, int $id): bool
    {
        try {
            DataBase::instance()->update(
                type: DataBaseType::main,
                table: DataBaseTable::account,
                values: ['auth_id' => $authId],
                where: new Where(
                    new CompareOperator(
                        field: 'id',
                        value: $id,
                        operator: OperationType::Equals))
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /** Получение bx_token у пользователя на платформе */
    public static function getBxToken(int $userId, string $platform): ?string
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::bitrix_token,
                where: new Where(
                    new CompareOperator(
                        field: 'hash',
                        value: md5($userId . $platform),
                        operator: OperationType::Equals
                    )),
                select_fields: ['token'],
                fetchColumn: true
            );

            return $data ?: null;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    private static function getField(string $field, mixed $value): ?AccountDTO
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::account,
                where: new Where(
                    new CompareOperator(
                        field: $field,
                        value: $value,
                        operator: OperationType::Equals
                    ))
            );
            return $data ? AccountDTO::fromArray(data: $data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }
}