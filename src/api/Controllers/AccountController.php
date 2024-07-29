<?php

namespace api\Controllers;

use api\Core\Models\Result;
use api\Services\AccountService;
use api\Services\SessionService;

class AccountController {
    public static function getList(?int $limit = null, ?int $page = null): Result
    {
        $accounts = AccountService::getList(limit: $limit, page: $page);

        $total = AccountService::getCount();
        return Result::do(
            status: true,
            data: [
                'accounts' => $accounts ?: [],
                'next' => $page * $limit < $total,
                'total' => $total
            ]
        );
    }

    public static function update(int $id, string $warehouse, int $role, int $active): Result
    {
        return AccountService::update(
            id: $id,
            warehouse: $warehouse,
            role: $role,
            active: $active
        );
    }

    public static function search(string $name): Result
    {
        $result = AccountService::search($name);
        return Result::do(true, $result);
    }

    public static function generate(int $id): Result
    {
        if (!AccountService::generate($id)) {
            return Result::error('failed.generate.account');
        }
        return Result::do(true);
    }

    public static function unAuth(): Result
    {
        if (!SessionService::deleteSession()) {
            return Result::error('auth.error.exit');
        }

        return Result::do(true);
    }
}
