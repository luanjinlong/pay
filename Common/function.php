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
            $cofnig = new \Common\Config($key);
            return $cofnig->getConfigs($key);
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

if (!function_exists('payLogger')) {

    /**
     * 支付过程出现的报错
     * @param $name
     * @param $message
     * @param $pay_name
     * @param $err_message
     * @throws Exception
     */
    function payLogger($name, $message, $pay_name, $err_message = [])
    {
        return logger($name)->debug($message, [
            'time' => date('Y-m-d H:i:s'),
            'pay_name' => $pay_name,
            'err_message' => $err_message
        ]);
    }
}

if (!function_exists('throwError')) {
    /**
     * @param $message
     * @throws Exception
     */
    function throwError($message)
    {
        if (DEBUG) {
            throw new Exception($message);
        }
        throw new Exception('网络异常，请联系客服,异常代码' . time());
    }
}
