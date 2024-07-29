<?php

namespace Database\Core\Models\Operators;

use Database\Core\Interfaces\Operator;
use Database\Core\Models\OperatorRelease;

class LogisticOperatorAnd implements Operator
{
    public function release(): OperatorRelease
    {
        return new OperatorRelease(query: "AND", params: []);
    }
}