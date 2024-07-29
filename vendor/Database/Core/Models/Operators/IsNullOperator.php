<?php

namespace Database\Core\Models\Operators;

use Database\Core\Enums\Condition\OperatorIsNullType;
use Database\Core\Interfaces\Operator;
use Database\Core\Models\OperatorRelease;

class IsNullOperator implements Operator
{
    public function __construct(
        public string $field,
        public OperatorIsNullType $type
    ) {}

    public function release(): OperatorRelease
    {
        return new OperatorRelease(
            query: $this->field . ' IS ' . $this->type->value,
            params: []
        );
    }
}