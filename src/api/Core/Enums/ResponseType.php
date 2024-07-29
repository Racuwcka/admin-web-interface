<?php

namespace api\Core\Enums;

enum ResponseType: string
{
    case json = 'json';
    case html = 'html';
}