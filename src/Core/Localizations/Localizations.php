<?php

namespace Core\Localizations;

class Localizations
{
    public static string $code;
    private static array $dictionary = [];

    public static function setup(string $code): bool
    {
        try {
            $localization_path = ROOT_DIRECTORY . "/src/Core/Localizations/Dictionary/$code.json";
            if (!file_exists($localization_path)) {
                return false;
            }

            $localization_file = file_get_contents($localization_path);
            self::$dictionary = json_decode($localization_file, true);
            self::$code = $code;

            return true;
        }
        catch (\Exception $e) {
            // TODO: Use a logger to log the error
            return false;
        }
    }

    public static function get($keyword, $values = null): string
    {
        if (isset(self::$dictionary[$keyword])) {
            if (!is_null($values)) {
                return self::replaceValuesString(self::$dictionary[$keyword], $values);
            }

            return self::$dictionary[$keyword];
        }

        return $keyword;
    }

    private static function replaceValuesString($str, $values)
    {
        try {
            $keys = array_map(function ($key) {
                return '{{' . $key . '}}';
            }, array_keys($values));
            return str_replace($keys, array_values($values), $str);
        } catch (\Exception $e) {
            return $str;
        }
    }
}