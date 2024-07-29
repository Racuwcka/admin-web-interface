<?php
namespace api\Core\Storage;

use api\Core\Enums\ResponseType;

class VariableStorage
{
    public static string $token;
    public static string $lang;

    public static ResponseType $responseType = ResponseType::json;
}