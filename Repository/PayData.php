<?php

namespace Repository;

/**
 * 从数据库获取支付数据的类
 * Class PayData
 * @package Repository
 */
class PayData
{

    /**
     * 从数据库中取出第三方对应的键和相应的值
     * @param $payName  string 支付名
     * @return array|string
     */
    public function getFieldBtPayName($payName)
    {
        //  todo 从数据库中取出一系列的对应第三方字段 此处 demo 我直接假设写数据
        return [
            'symbol' => '#',
            'encrypt_type' => 'md5',
            'request_method' => ['get', 'post'],
            'rule' => 'sort',
            'request_data' => '{}', // 参与请求的字段，这个是 json 格式
            'request_type' => 'curl', // 请求支付的方式
            'pay_money' => 'money',
            'encrypt_field' => 'encrypt', // 加密字段配置
        ];
        // 如果没有数据 抛出异常 应该是没有配置这个支付
    }

}