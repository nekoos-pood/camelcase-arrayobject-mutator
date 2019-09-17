<?php

if (!function_exists('camel_case')) {

    /**
     * @param string $value
     *
     * @return string
     */
    function camel_case($value)
    {
        static $cache = null;
        if (isset($cache[$value])) {
            return $cache[$value];
        }

        return $cache[$value] = lcfirst(studly_case($value));
    }
}

if (!function_exists('studly_case')) {

    /**
     * @param string $value
     *
     * @return string
     */
    function studly_case($value)
    {
        static $cache = null;

        $key = $value;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return $cache[$key] = str_replace(' ', '', $value);
    }
}