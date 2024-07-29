<?php

namespace Requests\Modules\LID;

use Requests\Core\AbstractClasses\RequestIndex;
use Requests\Modules\Logistics\RequestLogisticsSend;
use Requests\Modules\LID\Parts\Common;

/**
 * @property Common $common
 * @property RequestLIDParams $params
 * @property RequestLogisticsSend $send
 */
class RequestLIDIndex extends RequestIndex
{
    protected Common $common;

    public function declareParams() : RequestLIDParams
    {
        return new RequestLIDParams();
    }

    public function declareSend() : RequestLIDSend
    {
        return new RequestLIDSend();
    }
}