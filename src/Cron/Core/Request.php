<?php

namespace Cron\Core;

use Requests\Modules\OneC\RequestOneCIndex;

class Request
{
    public static RequestOneCIndex $oneC;

    public static function setupOneC(
        string $url,
        string $login,
        string $password,
        string $user,
        string $warehouse,
        string $lang
    ): bool
    {
        try {
            $request = new RequestOneCIndex();
            $request->params->set(
                url: $url,
                login: $login,
                password: $password,
                user: $user,
                warehouse: $warehouse,
                lang: $lang
            );
            self::$oneC = $request;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}