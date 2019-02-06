<?php


if (!function_exists('config')) {
    function config($path)
    {
        //  todo . 语法的现在还不知道怎么做
        if (strpos($path, '.') !== 0) {
            $arr = explode('.', $path);
            $file_name = $arr[0];
            $config = new \Common\Config($file_name);

            $key = array_shift($arr);
            static $new = [];
            while ($arr) {
                $key = array_shift($arr);
                $config[$key];
            }
        }
        return new \Common\Config($path);
    }
}
