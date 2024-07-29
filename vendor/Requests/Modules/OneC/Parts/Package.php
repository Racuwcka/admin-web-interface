<?php

namespace Requests\Modules\OneC\Parts;
use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Package extends RequestPart
{
    public function updateExternalMovingStatus(int $type, string $status, string $documentId): RequestResult
    {
        $args = [
            "type" => $type,
            "status" => $status,
            "guide" => $documentId
        ];

        $result = $this->send->send(
            methodName: "SetStatus",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['success']) && $result['success']) {
            return new RequestResult(
                status: true
            );
        }

        return new RequestResult(
            status: false,
            message: '' // TODO
        );
    }
}