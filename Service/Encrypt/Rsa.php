<?php

namespace Service\Encrypt;

/**
 * md5 加密方式的请求处理
 * Class Rsa
 */
class Rsa extends \Controller implements EncryptInterface
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
     * 获取加密处理后的数据
     * @return bool|string
     */
    public function getEncryptPayData()
    {
        // TODO: Implement getEncryptPayData() method.
    }

}