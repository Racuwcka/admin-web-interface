<?php

namespace Requests\Modules\OneC\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Product extends RequestPart
{
    public function info(string $barcode): RequestResult
    {
        $args = [
            "barcode" => $barcode,
        ];

        $result = $this->send->send(
            methodName: "InfoBarcode",
            type: RequestType::POST,
            args: $args
        );

        if ($result && isset($result['data'])) {
            return new RequestResult(
                status: true,
                data: $result['data']
            );
        }

        return new RequestResult(
            status: false,
            message: 'product.error.uknown_data'
        );
    }
}