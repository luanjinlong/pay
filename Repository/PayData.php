<?php

namespace Repository;

/**
 * 从数据库获取支付数据的类
 * Class PayData
 * @package Repository
 */
class PayData extends \Controller
{

    /**
     *  支付方法字段  get post
     */
    const REQUEST_METHOD = 'request_method';

    /**
     *  支付方式 curl
     */
    const REQUEST_TYPE = 'request_type';

    /**
     * 数据库字段
     * @var array
     */
    private $field;

    /**
     * 支付名
     * @var static
     */
    private $pay_name;

    /**
     * 设置支付名
     * @param $pay_name
     * @return $this
     */
    public function setPayName($pay_name)
    {
        $this->pay_name = $pay_name;
        return $this;
    }

    /**
     * 从数据库中取出第三方对应的键和相应的值
     * @return array|string|boolean
     */
    public function getFieldBtPayName()
    {
        //  todo 从数据库中取出一系列的对应第三方字段 此处 demo 我直接假设写数据

        if ($this->field) {
            return $this->field;
        }

        if (!$this->pay_name) {
            $this->errMessage = '请传入支付名';
            return false;
        }

        return $this->field = [
            'symbol' => '#',
            'encrypt_type' => 'md5',
            'request_method' => 'post',
            'rule' => 'k_sort',
            'request_data' => '{"re":"long","0":22}', // 参与请求的字段，逗号分隔的字符串
            'request_type' => 'curl', // 请求支付的方式
            'pay_money' => 'money',
            'encrypt_field' => 'encrypt', // 加密字段配置
        ];
        // 如果没有数据 抛出异常 应该是没有配置这个支付
    }

    /**
     * 获取支付方法 get|post
     * @return string|boolean
     */
    public function getRequestMethod()
    {
        $this->setPayName($this->pay_name)->getFieldBtPayName();
        if (!$this->field) {
            return false;
        }
        return $this->field[self::REQUEST_METHOD];
    }

    /**
     * 获取支付方式 curl
     * @return string|boolean
     */
    public function getRequestType()
    {
        $this->setPayName($this->pay_name)->getFieldBtPayName();
        if (!$this->field) {
            return false;
        }
        return $this->field[self::REQUEST_TYPE];
    }

}