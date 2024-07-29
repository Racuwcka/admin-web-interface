<?php

namespace Database\Core\Enums;

enum DataBaseOperatorWhereType: string
{
    case in = "IN";
    case notIn = "NOT IN";
}