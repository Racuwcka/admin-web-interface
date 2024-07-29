<?php

namespace Requests\Modules\OneC\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Cell extends RequestPart
{

    public function get(string $cell): RequestResult
    {
        $args = [
            "cell" => $cell
        ];

        $result = $this->send->send(
            methodName: "Orders_info",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['cell']) &&
            isset($result['warehouse']) &&
            isset($result['data']) &&
            !empty($result['cell_name'])) {
            return new RequestResult(
                status: true,
                data: $result
            );
        }

        return new RequestResult(
            status: false,
            message: 'cell.error.1c'
        );
    }

    public function put(string $cell, array $itemsList): RequestResult
    {
        $args = [
            "stockControl" => false,
            "type" => 1,
            "cell" => $cell,
            "items" => $itemsList
        ];

        $result = $this->send->send(
            methodName: "Cells_All_Functon",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['Status']) && $result['Status']) {
            return new RequestResult(
                status: true
            );
        }

        return new RequestResult(
            status: false,
            message: "cell.failed.put"
        );
    }

    public function putMultiply(array $itemsList): RequestResult
    {
        $args = [
            "stockControl" => false,
            "type" => 1,
            "items" => $itemsList
        ];

        $result = $this->send->send(
            methodName: "Cells_All_Functon",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['Status']) && $result['Status']) {
            return new RequestResult(
                status: true
            );
        }

        return new RequestResult(
            status: false,
            message: 'cell.failed.put_multiply'
        );
    }

    public function take(string $cell, array $itemsList): RequestResult
    {
        $args = [
            "stockControl" => false,
            "type" => 4,
            "cell" => $cell,
            "items" => $itemsList,
        ];

        $result = $this->send->send(
            methodName: "Cells_All_Functon",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['guid']) && isset($result['name'])) {
            return new RequestResult(
                status: true
            );
        }

        return new RequestResult(
            status: false,
            message: 'cell.failed.take'
        );
    }

    public function moveToAnotherCell(array $itemsList, string $cellFrom, string $cellTo, ?int $activityId = null): RequestResult
    {
        $args = [
            "stockControl" => false,
            "type" => 3,
            "items" => $itemsList,
            "cell_from" => $cellFrom,
            "cell_to" => $cellTo,
        ];

        if (isset($activityId)) {
            $args["ID_Site"] = $activityId;
        }

        $result = $this->send->send(
            methodName: "Cells_All_Functon",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['Status']) && $result['Status']) {
            return new RequestResult(
                status: true
            );
        }

        return new RequestResult(
            status: false,
            message: $result['error'] ?? 'cell.filed.moving'
        );
    }

    public function movingMultiply(array $itemsList): RequestResult
    {
        $args = [
            "stockControl" => false,
            "type" => 3,
            "items" => $itemsList,
        ];

        $result = $this->send->send(
            methodName: "Cells_All_Functon",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['Status']) && $result['Status']) {
            return new RequestResult(
                status: true
            );
        }

        return new RequestResult(
            status: false,
            message: '' // TODO
        );
    }

    public function correctionRezerve(string $documentId, string $barcode, int $availableQty, string $cell): RequestResult
    {
        $args = [
            "type" => 2,
            "documentId" => $documentId,
            "barcode" => $barcode,
            "amount" => $availableQty,
            "cell" => $cell
        ];

        $result = $this->send->send(
            methodName: "Cells_All_Functon",
            type: RequestType::POST,
            args: $args
        );

        if (isset($result['cells']) && isset($result['removed'])) {
            return new RequestResult(
                status: true,
                data: $result
            );
        }

        return new RequestResult(
            status: false,
            message: "cell.error.replace_rezerve_1c"
        );
    }
}