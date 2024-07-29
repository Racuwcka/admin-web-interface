<?php

spl_autoload_register(function($class_name) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/vendor/' . str_replace('\\', '/', $class_name).'.php';
    if (file_exists($path)) {
        require_once($path);
    }
});