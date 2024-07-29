<?php

namespace Requests\Modules\OneC\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class MovingWarehouse extends RequestPart
{
    public function create(
        string $warehouseFrom,
        string $warehouseTo,
        array $barcodeList,
        bool $movingAccept,
        bool $movingExternal,
        string $movingUid
    ): RequestResult
    {
        $args = [
            "warehouse_from" => $warehouseFrom,
            "warehouse_to" => $warehouseTo,
            "items" => $barcodeList,
            "accept" => $movingAccept,
            "external" => $movingExternal,
            "uuid" => $movingUid
        ];

        $result = $this->send->send(
            methodName: "MovingWarehouse",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['createReceiptOrderId']) &&
            isset($result['createReceiptOrderName']) &&
            isset($result['createDocumentId']) &&
            isset($result['createDocumentName'])) {
            return new RequestResult(
                status: true,
                data: $result
            );
        }

        return new RequestResult(
            status: false,
            message: $result['Error'] ?? 'system.error.1c'
        );
    }
}