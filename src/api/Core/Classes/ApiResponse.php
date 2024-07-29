<?php

namespace api\Core\Classes;

use api\Core\Enums\ResponseType;
use api\Core\Models\Result;
use api\Core\Storage\SessionStorage;
use api\Core\Storage\VariableStorage;
use api\Services\TemplateService;
use JetBrains\PhpStorm\NoReturn;

class ApiResponse
{
    #[NoReturn] public static function send(Result $data): void
    {
        if (VariableStorage::$responseType == ResponseType::html) {
            TemplateService::response($data->getMessagesText());
            exit();
        }
        else if (VariableStorage::$responseType == ResponseType::json) {
            $data = $data->jsonSerialize();

            $data["is_server"] = true;
            $data["is_auth"] = SessionStorage::has();

            http_response_code(200);
            echo json_encode($data);
            exit();
        }
    }

    #[NoReturn] public static function error(string $text): void
    {
        self::send(Result::error(message: $text));
    }
}