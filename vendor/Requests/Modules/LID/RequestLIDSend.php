<?php

namespace Requests\Modules\LID;

use Requests\Core\AbstractClasses\RequestSend;
use Requests\Core\Classes\Headers;
use Requests\Core\Classes\Http;
use Requests\Core\Enums\RequestContentType;
use Requests\Core\Enums\RequestType;

/**
 * @property RequestLIDParams $params
 **/
class RequestLIDSend extends RequestSend
{
    public function send(string $action, RequestType $requestType, array $args = []) : false | array
    {
        if (!$this->issetParams()) {
            return false;
        }

        $args["action"] = $action;
        $args["client_id"] = $this->params->clientId;

        $headers = new Headers();
        $headers->setContentType(RequestContentType::multipartFormData);

        $request = new Http(
            uri: "https://passport.lichi.one/request.php",
            type: $requestType
        );

        $request
            ->setArgs($args)
            ->setHeaders($headers);

        $result = $request->send();
        $args['token'] = 'secret';
        $this->logger->send(tag: '[LID]', uri: "https://passport.lichi.one/request.php", args: $args, result: $result);

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