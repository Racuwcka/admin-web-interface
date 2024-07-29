<?php

namespace Requests\Modules\Logistics;

use Requests\Core\AbstractClasses\RequestParams;

class RequestLogisticsParams extends RequestParams
{
    public string $url;
    public string $lang;
    public string $userData;

    public function set(
        string $url,
        string $lang,
        string $userData
    ): void
    {
        $this->url = $url;
        $this->lang = $lang;
        $this->userData = $userData;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

    public function setUserData(string $userData): void
    {
        $this->userData = $userData;
    }
}