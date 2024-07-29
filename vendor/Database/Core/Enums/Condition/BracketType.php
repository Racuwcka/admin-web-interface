<?php

namespace Database\Core\Enums\Condition;

enum BracketType: string {
    case OpenBracket = '(';
    case CloseBracket = ')';

    public static function open(?self $bracketType): string
    {
        return $bracketType === BracketType::OpenBracket ? $bracketType->value : '';
    }

    public static function close(?self $bracketType): string
    {
        return $bracketType === BracketType::CloseBracket ? $bracketType->value : '';
    }
}
