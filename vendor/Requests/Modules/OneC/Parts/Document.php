<?php

namespace Requests\Modules\OneC\Parts;
use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Document extends RequestPart
{
    public function getActiveListDocuments(int $type, ?string $warehouseFrom, ?string $warehouseTo): RequestResult
    {
        $args = [
            "type" => $type
        ];

        if ($warehouseFrom != null) {
            $args['warehouse_from'] = $warehouseFrom;
        }

        if ($warehouseTo != null) {
            $args['warehouse_to'] = $warehouseTo;
        }

        $result = $this->send->send(
            methodName: 'Open_All_Documents',
            type: RequestType::POST,
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
            message: '' // TODO
        );
    }

    public function search(int $type, string $query, int $year): RequestResult
    {
        $args = [
            "type" => $type,
            "Query" => $query,
            "yeardate" => $year
        ];

        $result = $this->send->send(
            methodName: 'Search_All_Documents',
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['data'][0])) {
            return new RequestResult(
                status: true,
                data: $result['data'][0]
            );
        }

        return new RequestResult(
            status: false,
            message: 'documentWasNotFound'
        );
    }

    public function getData(int $type, string $id): RequestResult
    {
        $args = [
            "type" => $type,
            "query" => $id
        ];

        $result = $this->send->send(
            methodName: 'Get_All_Documents',
            type: RequestType::POST,
            args: $args
        );

        if (isset($data['items'])) {
            return new RequestResult(
                status: true,
                data: $result['items']
            );
        }

        return new RequestResult(
            status: false,
            message: '' // TODO
        );
    }

    public function send(int $type, string $id, array $itemsList, ?string $area, ?string $packageId, bool $external): RequestResult
    {
        $args = [
            "type" => $type,
            "guide" => $id,
            "items" => $itemsList,
            "external" => $external
        ];

        if (!is_null($area)) {
            $args["area"] = $area;
        }

        if (!is_null($packageId)) {
            $args["packageId"] = $packageId;
        }

        $result = $this->send->send(
            methodName: 'CreateGoodsReceiptNote_All_Documents',
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['success']) && $result['success'] && !isset($result['Error']) && !isset($result['error'])) {
            return new RequestResult(
                status: true,
                data: $result
            );
        }

        return new RequestResult(
            status: false,
            message: $result['Error'] ?? $result['error'] ?? 'system.error.1c'
        );
    }
}