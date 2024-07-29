<?php

namespace Database\Core\Enums\Condition;

enum OperatorEntryType: string
{
    case In = 'IN';
    case NotIn = 'NOT IN';
}