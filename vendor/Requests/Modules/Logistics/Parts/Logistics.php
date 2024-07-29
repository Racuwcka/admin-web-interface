<?php

namespace Requests\Modules\Logistics\Parts;

use Requests\Core\AbstractClasses\RequestPart;
use Requests\Core\Enums\Logistics\ConfirmEntity;
use Requests\Core\Enums\RequestContentType;
use Requests\Core\Enums\RequestType;
use Requests\Core\Models\RequestResult;

class Logistics extends RequestPart
{
    public function getList(string $typeList, string $point, string $id): RequestResult
    {
        $args = [
            "do" => $typeList,
            "json_data" => json_encode([
                "point" => $point,
                "search" => [
                    "value" => $id
                ]
            ])
        ];

        $result = $this->send->send(
            contentType: RequestContentType::multipartFormData,
            requestType: RequestType::POST,
            args: $args,
            app: "logistics"
        );

        if (isset($result['api_success']) && $result['api_success']) {
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

    public function getCounter(int $point): RequestResult
    {
        $args = [
            "do" => "count",
            "point" => $point
        ];

        $result = $this->send->send(
            contentType: RequestContentType::multipartFormData,
            requestType: RequestType::POST,
            args: $args,
            app: "logistic"
        );

        if (isset($result['api_success']) &&
            isset($result['data']['availability_list']) &&
            isset($result['data']['withdraw_list']) &&
            isset($result['data']['send_list']) &&
            $result['api_success']) {
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

    public function getListOrder(string $typeList, string $point, int $start, int $length, string $querySearch): RequestResult
    {
        $args = [
            "do" => $typeList,
            "json_data" => json_encode([
                "point" => $point,
                "start" => $start,
                "length" => $length,
                "search" => [
                    "value" => $querySearch,
                    "regex" => false
                ]
            ])
        ];

        $result = $this->send->send(
            contentType: RequestContentType::multipartFormData,
            requestType: RequestType::POST,
            args: $args,
            app: "logistics"
        );

        if (isset($result['api_success']) && $result['api_success']) {
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

    public function confirm(
        int $answer,
        ?int $cellId,
        ?int $reasonRejection,
        ConfirmEntity $confirmEntity,
        int $confirmId
    ): RequestResult
    {
        $args = [
            "do" => "confirm_send",
            "comment" => "",
            "answer" => $answer
        ];

        if (!is_null($cellId)) {
            $args['cell'] = $cellId;
        }

        if (!is_null($reasonRejection)) {
            $args['reason-rejection'] = $reasonRejection;
        }

        $args[$confirmEntity->value] = $confirmId;

        $result = $this->send->send(
            contentType: RequestContentType::multipartFormData,
            requestType: RequestType::POST,
            args: $args,
            app: "logistics"
        );

        if (isset($result['api_success']) && $result['api_success']) {
            return new RequestResult(
                status: true
            );
        }

        return new RequestResult(
            status: false,
            message: $result['api_messages'][0] ?? 'couldConfirm'
        );
    }
}