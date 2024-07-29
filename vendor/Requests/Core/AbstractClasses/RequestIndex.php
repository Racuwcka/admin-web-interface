<?php

namespace Requests\Core\AbstractClasses;

use ReflectionProperty;

abstract class RequestIndex implements \Requests\Core\Interfaces\RequestIndex
{
    protected RequestSend $send;
    protected RequestParams $params;

    public function __construct() {
        $this->params = $this->declareParams();
        $this->send = $this->declareSend();
        $this->send->setParams($this->params);
    }

    /**
     * @throws \ReflectionException
     */
    public function __get(string $name)
    {
        if (!isset($this->$name)) {
            $rp = new ReflectionProperty(get_class($this), $name);
            $className = $rp->getType()->getName();
            $this->$name = new $className($this->send);
        }

        return  $this->$name;
    }
}