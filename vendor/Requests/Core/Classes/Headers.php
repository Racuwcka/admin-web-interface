<?php

namespace Requests\Core\Classes;

use Requests\Core\Enums\RequestContentType;

class Headers
{
    public ?RequestContentType $contentType;
    private array $headers = [];

    public function setContentType(RequestContentType $contentType): Headers
    {
        $this->contentType = $contentType;
        $this->headers[] = "Content-Type: $contentType->value";
        return $this;
    }

    public function get(): array
    {
        return $this->headers;
    }
}