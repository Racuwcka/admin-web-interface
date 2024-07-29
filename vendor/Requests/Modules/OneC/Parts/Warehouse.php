<?php

namespace Requests\Modules\OneC\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Warehouse extends RequestPart
{
    public function getRemains(string $warehouse, array $excludeCategory, ?int $onCurrentDate): RequestResult
    {
        $args = [
            "itemgroups_no" => $excludeCategory,
        ];

        if (!is_null($onCurrentDate)) {
            $args['DataTime'] = "$onCurrentDate";
        }

        $result = $this->send->send(
            methodName: 'Goods_remains/' . $warehouse,
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['remains'])) {
            return new RequestResult(
                status: true,
                data: $result
            );
        }

        return new RequestResult(
            status: false,
            message: '', // TODO
        );
    }
}