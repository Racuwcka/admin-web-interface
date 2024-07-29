<?php

namespace api\Controllers;

use api\Core\Models\LichiIdData;
use api\Core\Models\Result;
use api\Core\Models\UserAuthData;
use api\Core\Repositories\Account\AccountRepository;
use api\Services\AccountService;
use api\Services\LichiIdService;
use api\Services\SessionService;
use api\Services\WarehouseService;
use Core\Services\EnvService;

class PassportLichiController {
    public static function getAuthInfo(): Result
    {
        $config = EnvService::get(
            path: CONFIG_SRC . '/.env.lichiid',
            keys: ['lichi_id_scopes', 'lichi_id_client']
        );

        if (!$config) {
            return Result::error(
                    message: 'user.auth.exception',
                    messageValues: ['code' => 1]
            );
        }

        $scopes = LichiIdService::scopeArray($config['lichi_id_scopes']);

        return Result::success(
            new LichiIdData(clientId: $config['lichi_id_client'], scopes: $scopes)
        );
    }

    public static function get(): Result
    {
        $userAuth = AccountService::getAuth();
        if (is_null($userAuth)) {
            return Result::error('auth.error');
        }

        return Result::success($userAuth);
    }

    public static function register(string $token): Result
    {
        try {
            $scopeList = LichiIdService::get($token);

            if (is_null($scopeList)) {
                return Result::error(
                    message: 'user.auth.exception',
                    messageValues: ['code' => 2]
                );
            }

            $accountDTO = AccountRepository::getByBxId($scopeList->id->id);

            if (is_null($accountDTO)) {
                if (!AccountService::create(scopeList: $scopeList, token: $token)) {
                    return Result::error(
                        message: 'user.auth.exception',
                        messageValues: ['code' => 3]
                    );
                }
                return Result::success(message: 'onRegistered');

            } else {

                if (!AccountService::checkRequiredFields($accountDTO)) {
                    return Result::success(message: 'onRegistered');
                }

                $accountWarehouse = WarehouseService::get(id: $accountDTO->warehouse);

                if (is_null($accountWarehouse)) {
                    return Result::error(
                        message: 'user.auth.exception',
                        messageValues: ['code' => 4]
                    );
                }

                $sessionWarehouse = $accountWarehouse->consolidated ? null : $accountWarehouse;

                $sessionToken = SessionService::create(
                    userId: $accountDTO->id,
                    warehouse: $sessionWarehouse
                );

                if (is_null($sessionToken)) {
                    return Result::error(
                        message: 'user.auth.exception',
                        messageValues: ['code' => 5]
                    );
                }

                $userAuth = AccountService::getAuth(token: $sessionToken, scopeList: $scopeList, bxToken: $token);

                if (is_null($userAuth)) {
                    return Result::error(
                        message: 'user.auth.exception',
                        messageValues: ['code' => 6]
                    );
                }

                return Result::success(
                    new UserAuthData(token: $sessionToken, userInfo: $userAuth)
                );
            }
        } catch (\Exception $e) {
            return Result::error(
                message: 'user.auth.exception',
                messageValues: ['code' => 7]
            );
        }
    }
}
