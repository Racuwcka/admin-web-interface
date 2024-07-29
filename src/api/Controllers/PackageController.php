<?php

namespace api\Controllers;

use api\Core\Enums\MessageType;
use api\Core\Enums\Queue\QueueStatusType;
use api\Core\Models\Message;
use api\Core\Models\Result;
use api\Services\PackageService;
use api\Services\QueueService;

class PackageController {

    public static function create(int $count): Result
    {
        return QueueService::create($count);
    }

    public static function getProcesses(int $page, int $limit): Result
    {
        $queue = QueueService::get(page: $page, limit: $limit);
        $total = QueueService::getCount();

        return Result::do(
            status: true,
            data: [
                'packages' => $queue ?: [],
                'next' => $page * $limit < $total,
                'total' => $total
            ]);
    }

    public static function download(int $id): Result
    {
        $root = ROOT_DIRECTORY . "/tmp/packages_$id/packages.pdf";
        if (!file_exists($root)) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: 'file.not.found')
            );
        }

        if (!QueueService::update(id: $id, statusType: QueueStatusType::downloaded)) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: 'error.download.file',
                    messageLocaleValues: ['code' => 1])
            );
        }

        if (!PackageService::download(id: $id, root: $root)) {
            return Result::do(
                status: false,
                message: Message::do(
                    type: MessageType::error,
                    messageLocaleKey: 'error.download.file',
                    messageLocaleValues: ['code' => 2])
            );
        }

        return Result::do(true);
    }
}
