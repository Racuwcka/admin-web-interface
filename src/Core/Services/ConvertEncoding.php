<?php

namespace Core\Services;

class ConvertEncoding
{
    public static function cp1251(string $text): string
    {
        return iconv('utf-8', 'cp1251', $text);
    }

    public static function ascii(string $text): string
    {
        return iconv('utf-8', 'ASCII//TRANSLIT', $text);
    }
}