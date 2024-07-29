<?php

namespace api\Controllers;

use api\Core\Enums\MessageType;
use api\Core\Models\Message;
use api\Core\Models\Result;
use api\Services\WorkAreaService;

class WorkareaController
{
    /** Получение списка рабочих зон */
    public static function getList(): Result
    {
        $workAreaList = WorkAreaService::getList();
        return Result::do(
            status: true,
            data: $workAreaList
        );
    }

    /** Поиск рабочих зон по названию */
    public static function search(string $name): Result
    {
        $result = WorkAreaService::search($name);
        return Result::do(true, $result);
    }

    /** Генерация и скачивание pdf файла qr-кода рабочей зон  */
    public static function generate(int $id): Result
    {
        if (!WorkAreaService::generate($id)) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: 'failed.generate.workarea')
            );
        }
        return Result::do(true);
    }
}