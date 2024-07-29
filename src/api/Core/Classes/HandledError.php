<?php

namespace api\Core\Classes;

use api\Core\Models\Result;

class HandledError extends \Error{
    public function __construct(
        string $message,
        private readonly array $messageValues = []
    )
    {
        parent::__construct($message);
    }

    public function getMessageValues(): array
    {
        return $this->messageValues;
    }

    public function resultError(): Result
    {
        return Result::error(
            message: $this->getMessage(),
            messageValues: $this->getMessageValues()
        );
    }
}