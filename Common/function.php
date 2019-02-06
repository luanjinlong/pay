<?php


if (!function_exists('config')) {
    function config($path)
    {
        return new \Common\Config($path);
    }
}
