<?php

namespace api\Services;

use Core\Localizations\Localizations;

class TemplateService
{
    public static function error(string $message): void
    {
        header('Content-Type: text/html');
        ob_start();
        include ROOT_DIRECTORY . '\src\templates\viewError.php';
        ob_get_contents();
        exit;
    }

    /**
     * @param array<string> $messages
     * @return void
     */
    public static function response(array $messages): void
    {
        $messagesEmptyText = Localizations::get('noMessages');

        header('Content-Type: text/html');
        ob_start();
        include ROOT_DIRECTORY . '/src/templates/response.php';
        echo ob_get_clean(); // выводит содержимое буфера и очищает его
    }
}