<?php

namespace PeteKlein\Performant\Utils;

class StringUtils {
    public static function camelCase(string $string)
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }
}
