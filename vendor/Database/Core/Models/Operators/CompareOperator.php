<?php

namespace Database\Core\Models\Operators;

use Database\Core\Enums\Condition\BracketType;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Interfaces\Operator;
use Database\Core\Models\OperatorRelease;

class CompareOperator implements Operator
{
    public function __construct(
        public string $field,
        public mixed $value,
        public OperationType $operator,
        public ?BracketType $bracket = null
    ) {}

    public function release(): OperatorRelease
    {
        return new OperatorRelease(
            query: BracketType::open($this->bracket) . $this->field . ' ' . $this->operator->value . ' ?' . BracketType::close($this->bracket),
            params: [$this->value]
        );
    }
}