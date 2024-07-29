<?php

namespace api\Services;

use api\Core\Enums\Queue\QueueStatusType;
use api\Core\Models\Queue\QueueOutput;
use api\Core\Models\Result;
use api\Core\Repositories\Queue\DTO\QueueDTO;
use api\Core\Repositories\Queue\QueueRepository;
use api\Core\Storage\SessionStorage;

class QueueService {
    public static function create($count): Result
    {
        if ($count < 1 || $count > 5000) {
            return Result::error('wrong.count');
        }

        $id = QueueRepository::create(
            new QueueDTO(
                name: 'package',
                user_id: SessionStorage::user()->id,
                status: QueueStatusType::created->value,
                count: $count,
                created_at: date("Y-m-d H:i:s"))
        );
        if (is_null($id)) {
            return Result::error('failed.create.package');
        }

        return Result::success(
            [
                'id' => $id,
                'status' => 'Ожидает',
                'count' => $count
            ]
        );
    }

    public static function update(int $id, QueueStatusType $statusType): bool
    {
        return QueueRepository::update(id: $id, statusType: $statusType);
    }

    /** @return array<QueueOutput> */
    public static function get(int $page, int $limit): array
    {
        $queueDTO = QueueRepository::get(
            name: 'package',
            userId: SessionStorage::user()->id,
            page: $page,
            limit: $limit
        );
        return QueueOutput::fromListDTO($queueDTO);
    }

    /** Получение количества задач */
    public static function getCount(): int
    {
        return QueueRepository::getCount(SessionStorage::user()->id);
    }
}