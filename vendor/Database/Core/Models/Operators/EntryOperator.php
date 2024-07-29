<?php

namespace Database\Core\Models\Operators;

use Database\Core\Enums\Condition\OperatorEntryType;
use Database\Core\Interfaces\Operator;
use Database\Core\Models\OperatorRelease;

class EntryOperator implements Operator
{
    public function __construct(
        public string            $field,
        public array             $value,
        public OperatorEntryType $type
    ) {}

    public function release(): OperatorRelease
    {
        $arrQuestion = array_fill(0, count($this->value), '?');

        return new OperatorRelease(
            query: $this->field . ' ' . $this->type->value . ' (' . implode(',', $arrQuestion) . ')',
            params: $this->value
        );
    }
}