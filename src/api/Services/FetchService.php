<?php

namespace api\Services;

use api\Core\Enums\MessageType;
use api\Core\Models\Message;
use api\Core\Models\Result;

class FetchService
{
    public static function getArgumentsApi($method, $modulePath): array {

        $params = [];

        $reflector = new \ReflectionClass($modulePath);
        foreach ($reflector->getMethod($method)->getParameters() as $param) {
            $params[$param->getName()] = [
                'type' => !is_null($param->getType()) ? $param->getType()->getName() : $param->getType(),
                'optional' => $param->isOptional(),
            ];
        }

        $opt = [];

        $setTypeParams = function(&$var, $type) {
            try {
                if ($type == 'bool') {
                    $var = strtolower($var) === 'true';
                }
            } catch (\Exception $e) {}
        };

        foreach ($params as $key => $item) {

            if (!isset($_REQUEST[$key]) && !$item['optional']) {
                die(json_encode(Result::do(
                    status: false,
                    message: Message::do(
                        type: MessageType::error,
                        messageLocaleKey: 'Не переданы обязательные параметры'
                    ))));
            } else {
                if ($item['optional']) {

                    if (isset($_REQUEST[$key])) {
                        $value = $_REQUEST[$key];
                    } else {
                        continue;
                    }
                } else {
                    $value = $_REQUEST[$key];
                }

                if (!is_null($item['type'])) {
                    $setTypeParams($value, $item['type']);
                }
                $opt[] = $value;
            }
        }
        return $opt;
    }
}