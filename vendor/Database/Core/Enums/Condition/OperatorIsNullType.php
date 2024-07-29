<?php

namespace Database\Core\Enums\Condition;

enum OperatorIsNullType: string
{
    case Null = 'NULL';
    case NotNull = 'NOT NULL';
}