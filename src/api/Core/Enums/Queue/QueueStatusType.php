<?php

namespace api\Core\Enums\Queue;

enum QueueStatusType: string {
    case done = 'done';
    case busy = 'busy';
    case rejected = 'rejected';
    case downloaded = 'downloaded';
    case created = 'created';
}