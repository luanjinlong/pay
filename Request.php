<?php

/**
 * 请求方式的类
 * Class Request
 */
class Request extends Controller
{

    /**
     * 请求方式的配置信息
     */
    const CONFIG = [
        'curl',
    ];

    /**
     * 请求方式是否符合需求
     * @return bool
     */
    public function isSupport($type)
    {
        if (!in_array($type, self::CONFIG)) {
            $this->errMessage = '请求方式不符合需求';
            return false;
        }
        return true;
    }

    /**
     * curl 的方式请求支付
     * @return boolean
     */
    public function curl()
    {
        return true;
    }
}