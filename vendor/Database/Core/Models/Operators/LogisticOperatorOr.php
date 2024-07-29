<?php

namespace Database\Core\Models\Operators;

use Database\Core\Interfaces\Operator;
use Database\Core\Models\OperatorRelease;

class LogisticOperatorOr implements Operator
{
    public function release(): OperatorRelease
    {
        return new OperatorRelease(query: "OR", params: []);
    }
}