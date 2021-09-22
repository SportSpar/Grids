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

        return $key;
    }
}