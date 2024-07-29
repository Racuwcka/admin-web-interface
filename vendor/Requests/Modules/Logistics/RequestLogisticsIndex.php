<?php

namespace Requests\Modules\Logistics;

use Requests\Core\AbstractClasses\RequestIndex;
use Requests\Modules\Logistics\Parts\Logistics;
use Requests\Modules\Logistics\Parts\TransitStorage;

/**
 * @property Logistics $logistics
 * @property TransitStorage $transitStorage
 *
 * @property RequestLogisticsParams $params
 * @property RequestLogisticsSend $send
 */
class RequestLogisticsIndex extends RequestIndex
{
    protected Logistics $logistics;
    protected TransitStorage $transitStorage;

    public function declareParams() : RequestLogisticsParams
    {
        return new RequestLogisticsParams();
    }

    public function declareSend() : RequestLogisticsSend
    {
        return new RequestLogisticsSend();
    }
}