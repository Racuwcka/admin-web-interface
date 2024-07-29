<?php

use Cron\Exec;

const CONFIG_SRC = __DIR__.'/config';
const VENDOR_SRC = __DIR__.'/vendor/';
const DIST_SRC = __DIR__.'/dist/';
const ROOT_DIRECTORY = __DIR__;

spl_autoload_register(function($class_name) {
    $folders = ["src", "vendor"];

    foreach ($folders as $folder) {
        $path = __DIR__ . '/' . $folder . '/' . str_replace('\\', '/', $class_name).'.php';
        if (file_exists($path)) {
            require_once($path);
            return;
        }
    }
});
$cli_args = ['source', 'args'];
$options = getopt('', array_map(fn($v) => $v . '::', $cli_args));

if (!isset($options['source']) && !isset($options['args'])) {
    throw new \Exception('There is no resource parameter or arguments');
}

$source = $options['source'];
$args = json_decode(unserialize(str_replace(' ', '"', $options['args'] ?? '{}')) , true);

if ($source == 'cron') new Exec(args: $args);