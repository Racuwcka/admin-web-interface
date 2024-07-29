<?php

namespace Requests\Modules\OneC\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class InventoryCell extends RequestPart
{
    public function findContentsInInactiveCells(string $inActiveCell): RequestResult
    {
        $args = [
            "cell" => $inActiveCell,
        ];

        $result = $this->send->send(
            methodName: "Orders_info",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['cell']) && isset($result['cell_name']) && isset($result['warehouse']) && isset($result['data'])) {
            return new RequestResult(
                status: true,
                data: $result
            );
        }

        return new RequestResult(
            status: false,
            message: '' // TODO
        );
    }
}