<?php

namespace Requests\Modules\OneC\Parts;
use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Common extends RequestPart
{
    public function createSubsort(string $warehouseFrom, string $warehouseTo, array $items): RequestResult
    {
        $args = [
            "type" => 1,
            "warehouse" => $warehouseFrom,
            "warehouseto" => $warehouseTo,
            "items" => $items,
        ];

        $result = $this->send->send(
            methodName: "CreateSubsort",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['data'])) {
            return new RequestResult(
                status: true,
                data: $result['data']
            );
        }

        return new RequestResult(
            status: false,
            message: '' // TODO
        );
    }

    public function syncNomenclature(): RequestResult
    {
        $args = [];

        $result = $this->send->send(
            methodName: "AllRecord",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['record'])) {
            return new RequestResult(
                status: true,
                data: $result['record']
            );
        }

        return new RequestResult(
            status: false,
            message: '' // TODO
        );
    }
}