<?php

namespace api\Core\Models\UserAuth;

use api\Core\Enums\AuthType;

class AuthInfo implements \JsonSerializable
{
    public function __construct(
        public AuthType $type,
        public ?string  $link
    ) {}

    public function jsonSerialize(): array
    {
        return [
            "type" => $this->type->value,
            "link" => $this->link,
        ];
    }
}