<?php

use api\Services\SystemService;
use Core\Localizations\Localizations;

const CONFIG_SRC = __DIR__.'/config';
const VENDOR_SRC = __DIR__.'/vendor/';
const DIST_SRC = __DIR__.'/dist/';
const ROOT_DIRECTORY = __DIR__;

require_once __DIR__ . '/autoload/autoloadSrc.php';
require_once __DIR__ . '/autoload/autoloadVendor.php';

$segments = array_slice(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), 1);
if (count($segments) < 1) {
    die('Отсутствуют необходимые параметры');
}

$source = $segments[0];

if ($source != "cron" &&
    $source != "migration" &&
    $source != "api") {
    SystemService::dieResult("The source is missing");
}

if ($source == 'cron') {
    new \Cron\Index();
}
else if ($source == "migration") {
    new \Migration\Index();
}
else if ($source == "api") {
    $lang = strtolower($_REQUEST['lang'] ?? '');

    if (!Localizations::setup($lang)) {
        die('language not specified');
    }

    if (count($segments) != 3) {
        die('Не хватает параметров');
    }

    $module = $segments[1];
    $method = $segments[2];

    $class = "api\\Index";
    if (!class_exists(($class))) {
        die('Не существует класса инициализации src');
    }
    new $class($module, $method);
}