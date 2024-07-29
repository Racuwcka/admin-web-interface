<?php

namespace Requests\Modules\Logistics;

use Requests\Core\AbstractClasses\RequestSend;
use Requests\Core\Classes\Headers;
use Requests\Core\Classes\Http;
use Requests\Core\Enums\RequestContentType;
use Requests\Core\Enums\RequestType;

/**
 * @property RequestLogisticsParams $params
**/

class RequestLogisticsSend extends RequestSend
{
    public function send(
        RequestContentType $contentType,
        RequestType $requestType,
        array $args = [],
        string $app = ''): false | array
    {
        if (!$this->issetParams()) {
            return false;
        }

        if (!empty($app)) {
            $args['app'] = $app;
            $args['IS_MOBILE'] = 'N';
            $args['LANGUAGE'] = $this->params->lang;
            $args['USER_DATA'] = $this->params->userData;
        }

        $headers = new Headers();
        $headers->setContentType($contentType);

        $request = new Http(
            uri: $this->params->url,
            type: $requestType
        );

        $request
            ->setArgs($args)
            ->setHeaders($headers);

        $result = $request->send();
        $args['USER_DATA'] = 'secret';
        $this->logger->send(tag: '[Logistics]', uri: $this->params->url, args: $args, result: $result);

        if (!$result) {
            return false;
        }

        $result = json_decode($result, TRUE);

        if (json_last_error() == JSON_ERROR_NONE && !is_null($result)) {
            return $result;
        }

        return false;
    }
}