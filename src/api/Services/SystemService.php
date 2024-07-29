<?php

namespace api\Services;

use api\Core\Enums\MessageType;
use api\Core\Models\Message;
use api\Core\Models\Result;
use JetBrains\PhpStorm\NoReturn;

class SystemService
{
    #[NoReturn] public static function dieResult(string $text): void
    {
        http_response_code(400);
        die(json_encode(Result::do(
            status: false,
            message: Message::do(
                type: MessageType::error,
                messageLocaleKey: $text
            ))));
    }
}