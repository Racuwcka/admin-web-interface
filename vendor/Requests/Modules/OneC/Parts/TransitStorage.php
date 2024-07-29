<?php

namespace Requests\Modules\OneC\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class TransitStorage extends RequestPart
{
    public function shipFromWarehouse(string $orderId, array $items): RequestResult
    {
        $args = [
            "orderId" => $orderId,
            "items" => $items,
        ];

        $result = $this->send->send(
            methodName: "Ship_the_package",
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
            message: $result['reason'] ?? 'orderNotShipped'
        );
    }
}