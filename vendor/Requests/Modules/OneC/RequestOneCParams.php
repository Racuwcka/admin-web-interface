<?php

namespace Requests\Modules\OneC;

use Requests\Core\AbstractClasses\RequestParams;

class RequestOneCParams extends RequestParams
{
    public string $url;
    public string $login;
    public string $password;
    public string $user;
    public string $warehouse;
    public string $lang;

    public function set(
        string $url,
        string $login,
        string $password,
        string $user,
        string $warehouse,
        string $lang
    ): void
    {
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
        $this->user = $user;
        $this->warehouse = $warehouse;
        $this->lang = $lang;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setAuth(string $login, string $password): void
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function setWarehouse(string $warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }
}