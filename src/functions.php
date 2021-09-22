<?php

use Illuminate\Container\Container;

if (!function_exists('__')) {
    function __($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return $key;
        }

        $container = Container::getInstance();
        if ($container->has('translator')) {
            return $container->get('translator')->get($key, $replace, $locale);
        }

        // A function from Laravel translator to replace placeholders
        $makeReplacements = static function ($line, array $replace) {
            if (empty($replace)) {
                return $line;
            }

            foreach ($replace as $key => $value) {
                $line = str_replace(
                    [':' . $key, ':' . strtoupper($key), ':' . ucfirst($key)],
                    [$value, strtoupper($value), ucfirst($value)],
                    $line
                );
            }

            return $line;
        };

        return $makeReplacements($key, $replace);
    }
}
