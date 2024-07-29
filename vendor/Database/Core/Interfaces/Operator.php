<?php

namespace Database\Core\Interfaces;

use Database\Core\Models\OperatorRelease;

interface Operator
{
    public function release(): OperatorRelease;
}