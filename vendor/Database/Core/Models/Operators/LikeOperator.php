<?php

namespace Database\Core\Models\Operators;

use Database\Core\Enums\Condition\BracketType;
use Database\Core\Interfaces\Operator;
use Database\Core\Models\OperatorRelease;

class LikeOperator implements Operator
{
    public function __construct(
        public string $field,
        public string $value,
        public ?BracketType $bracket = null
    ) {}

    public function release(): OperatorRelease
    {
        return new OperatorRelease(
            query: BracketType::open($this->bracket) . "`$this->field` LIKE ?" . BracketType::close($this->bracket),
            params: [$this->value]
        );
    }
}