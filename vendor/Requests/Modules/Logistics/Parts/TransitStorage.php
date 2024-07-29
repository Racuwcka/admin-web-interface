<?php

namespace Requests\Modules\Logistics\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestContentType;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class TransitStorage extends RequestPart
{
    public function get(string $orderId): RequestResult
    {
        $args = [
            "app" => "logistic",
            "no_bx" => 1,
            "do" => "get_package_products",
            "code" => $orderId
        ];

        $result = $this->send->send(
            contentType: RequestContentType::textJson,
            requestType: RequestType::GET,
            args: $args
        );

        if (isset($result['data']['products']) && isset($result['data']['is_canceled'])) {
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
}