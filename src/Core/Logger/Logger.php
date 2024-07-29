<?php

namespace Core\Logger;

class Logger
{
    public static function logger(string $dir, string $filename, string $text, int $flag = FILE_APPEND): void
    {
        if (!file_exists(ROOT_DIRECTORY . '/logs/' . $dir)) {
            mkdir(ROOT_DIRECTORY . '/logs/' . $dir);
        }

        file_put_contents(
            filename: ROOT_DIRECTORY . '/logs/' . $dir . '/' . $filename . '.log',
            data: $text . PHP_EOL,
            flags: $flag
        );
    }
}