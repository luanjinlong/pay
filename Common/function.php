<?php


if (!function_exists('config')) {
    /**
     * 获取配置
     * @param $key
     * @return array
     * @throws Exception
     */
    function config($key)
    {
        if (strpos($key, '.') === false) {
            // 直接返回整个文件的配置
            $config = new \Common\Config($key);
            return $config->getConfigs($key);
        }

        $arr = explode('.', $key);
        // 数组的第一个键就是文件名
        $file_name = array_shift($arr);
        $config = new \Common\Config($file_name);
        $config_data = $config->getConfigs($file_name);

        foreach ($arr as $segment) {
            $config_data = $config_data[$segment];
        }
        return $config_data;
    }
}

if (!function_exists('logger')) {

    /**
     * @param $name
     * @return \Common\Log
     * @throws Exception
     */
    function logger($name)
    {
        return new \Common\Log($name);
    }
}

