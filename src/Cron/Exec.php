<?php

namespace Cron;

use Cron\Core\DataBase;
use Cron\Core\Request;

class Exec {
    public function __construct(array $args)
    {
        if (!isset($args['module']) && !isset($args['method'])) {
            throw new \Exception('The module parameter or method is missing');
        }
        $module = ucfirst($args['module']); unset($args['module']);
        $method = $args['method']; unset($args['method']);

        DataBase::setUp();

        Request::setupOneC(
            url: 'http://192.168.200.5/UT114TestBackup/hs',
            login: 'web',
            password: '',
            user: 'Test',
            warehouse: '03e8e094-cc25-11eb-b9ce-9d7f16b71e89',
            lang: 'ru'
        );

        $class = "Cron\\Modules\\" . $module;

        if (!class_exists($class)) {
            die('Не существует класса ' . $class);
        }

        (new $class)->{$method}(...$args);
    }
}