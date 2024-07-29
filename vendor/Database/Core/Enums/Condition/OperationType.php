<?php

namespace Database\Core\Enums\Condition;

enum OperationType: string
{
    case Equals = '=';
    case Greater = '>';
    case Less = '<';
    case GreaterEquals = '>=';
    case LessEquals = '<=';
}
