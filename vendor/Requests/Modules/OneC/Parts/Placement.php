<?php

namespace Requests\Modules\OneC\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Placement extends RequestPart
{
    public function getPlacementItems(array $barcodes, int $amount): RequestResult
    {
        $args = [
            "barcodes" => array_keys($barcodes),
            "amount_otbor" => $amount
        ];

        $result = $this->send->send(
            methodName: 'getPlacementItems',
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['barcodes']) && count($result['barcodes']) > 0) {
            return new RequestResult(
                status: true,
                data: $result['barcodes']
            );
        }

        return new RequestResult(
            status: false,
            message: '', // TODO
        );
    }
}