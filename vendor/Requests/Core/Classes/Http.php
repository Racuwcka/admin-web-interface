<?php

namespace Requests\Core\Classes;
use Requests\Core\Enums\RequestContentType;
use Requests\Core\Enums\RequestType;

class Http
{
    private ?Headers $headers;
    private array $args = [];
    private string $login = '';
    private string $password = '';

    public function __construct(
        private string $uri,
        private readonly RequestType $type
    ) {}

    public function setHeaders(Headers $headers): Http
    {
        $this->headers = $headers;
        return $this;
    }

    public function setArgs(array $args): Http
    {
        $this->args = $args;
        return $this;
    }

    public function setAuth(string $login, string $password): Http
    {
        $this->login = $login;
        $this->password = $password;
        return $this;
    }

    public function send() : bool|string
    {
        if (!isset($this->uri) ||
            !isset($this->type)) {
            return false;
        }

        $oCurl = curl_init();
        if ($this->type == RequestType::GET) {
            $this->uri .= '?' . http_build_query($this->args);
        }

        $headers = [];

        /** @var ?RequestContentType $contentType */
        $contentType = null;

        if ($this->headers) {
            $headers = $this->headers->get();
            $contentType = $this->headers->contentType;
        }

        curl_setopt_array($oCurl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERPWD => "$this->login:$this->password",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->uri,
            CURLOPT_POST => $this->type == RequestType::POST
        ]);

        if ($this->type == RequestType::POST) {
            if ($contentType == RequestContentType::multipartFormData) {
                curl_setopt($oCurl, CURLOPT_POSTFIELDS, $this->args);
            }
            else {
                curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode($this->args, JSON_UNESCAPED_UNICODE));
            }
        }

        $result = curl_exec($oCurl);
        curl_close($oCurl);

        return $result;
    }
}