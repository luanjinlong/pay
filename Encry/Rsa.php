<?php

namespace Encry;

/**
 * md5 加密方式的请求处理
 * Class Rsa
 */
class Rsa extends \Controller implements EncryInterface
{
    /**
     * md5方式加密配置
     */
    const CONFIG = [
        'sql_type' => 2, // 在数据库中的数字代码
        'symbol' => ['%', '#', '^'], // 使用什么符号拼接
    ];

    /**
     * 加密拼接符号
     * @var string
     */
    private $field;

    /**
     * 设置第三方对应的支付字段
     * @param $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * 请求支付
     * @return bool|string
     */
    public function pay()
    {
        return true;

    }

    public function notify()
    {

    }

    public function sync()
    {

    }


}