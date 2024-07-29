<?php

namespace Requests\Modules\OneC;

use Requests\Core\AbstractClasses\RequestSend;
use Requests\Core\Classes\Headers;
use Requests\Core\Classes\Http;
use Requests\Core\Enums\RequestContentType;
use Requests\Core\Enums\RequestType;

/**
 * @property RequestOneCParams $params
 **/

class RequestOneCSend extends RequestSend
{
    public function send(string $methodName, RequestType $type, array $args = []): false | array
    {
        if (!$this->issetParams()) {
            return false;
        }

        $args['lang'] = $this->params->lang;
        $args['avtor'] = $this->params->user;
        $args['warehouse'] = $args['warehouse'] ?? $this->params->warehouse;

        $uri = $this->params->url . "/API_TSD/" . $methodName;

        $headers = new Headers();
        $headers->setContentType(RequestContentType::textJson);

        $request = new Http(
            uri: $uri,
            type: $type
        );

        $request
            ->setArgs($args)
            ->setHeaders($headers)
            ->setAuth(
                login: $this->params->login,
                password: $this->params->password
            );

        $result = $request->send();
        $this->logger->send(tag: '[1C]', uri: $uri, args: $args, result: $result);

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