<?php

namespace Requests\Modules\LID\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Common extends RequestPart
{
    public function get(string $token): RequestResult
    {
        $args = [
            "token" => $token
        ];

        $result = $this->send->send(
            action: "get",
            requestType: RequestType::POST,
            args: $args
        );

        if (isset($result['data']) && is_array($result['data'])) {
            return new RequestResult(
                status: true,
                data: $result['data']
            );
        }

        return new RequestResult(
            status: false,
            message: 'notFoundDataBitrix'
        );
    }
}