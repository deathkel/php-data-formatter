<?php
if (!function_exists('str2Camel')) {
    function str2Camel($str)
    {
        return lcfirst(ucwords(str_replace(['-', '_'], ' ', $str)));
    }
}

if (!function_exists('str2BigCamel')) {
    function str2BigCamel($str)
    {
        return ucwords(str_replace(['-', '_'], ' ', $str));
    }
}

if (!function_exists('str2Snake')) {
    function str2Snake($str, $delimiter = '_')
    {
        if (!ctype_lower($str)) {
            $str = preg_replace('/\s+/u', '', ucwords($str));

            $str = mb_strtolower(
                (preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $str)),
                'UTF-8');
        }
        return $str;
    }
}