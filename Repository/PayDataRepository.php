<?php

namespace Repository;

/**
 * 支付名对应的数据仓库
 * Class PayDataRepository
 * @package Repository
 */
class PayDataRepository extends \Controller
{

    /**
     *  支付方法字段  get post
     */
    const REQUEST_METHOD = 'request_method';

    /**
     *  支付字符按
     */
    const PAY_NAME = 'pay_name';

    /**
     * 加密方式的字段  这个是数据库的字段
     */
    const ENCRYPT_TYPE = 'encrypt_type';

    /**
     *  支付请求地址 的字段
     */
    const REQUEST_URL = 'request_url';

    /**
     *  异步回掉地址 的字段
     */
    const ASYNC_URL = 'async_url';

    /**
     * 同步回掉 的字段
     */
    const CALLBACK_URL = 'callback_url';

    /**
     * 参与请求的字段
     */
    const REQUEST_FIELD = 'request_field';

    /**
     * 订单号字段
     */
    const ORDER_NUM = 'order_num';

    /**
     * 数据库字段
     * @var array
     */
    private $field;

    /**
     * todo  这个数据表其实是两部分内容  一部分是请求参数的字段  另一部分相当于是配置字段 比如加密规则，加密字段等
     * 从数据库中取出第三方对应的键和相应的值
     * @param $payName
     * @return array|string|boolean
     */
    public function getFieldByPayName($payName)
    {
        //  todo 从数据库中取出一系列的对应第三方字段 此处 demo 我直接假设写数据

        if ($this->field) {
            return $this->field;
        }

        if (!$payName) {
            $this->errMessage = '请传入支付名';
            return false;
        }

        return $this->field = [
            'pay_name' => 'test', //支付名称
            'symbol' => '#',
            'request_method' => 'get',
            'rule' => 'k_sort',
            'request_field' => 'get,money,encrypt', // 参与请求的字段，逗号分隔的字符串
            'request_url' => 'http://www.baidu.com', //请求的地址
            'async_url' => '', //异步回掉地址
            'callback_url' => '', //同步回掉地址
            'pay_money' => 'money',
            'encrypt_type' => 'md5', // 加密方式
            'encrypt_data' => 'get,money,encrypt', // 参与加密的字段，逗号分隔的字符串
            'encrypt_field' => 'encrypt', // 加密字段配置
            'encrypt_rule' => 'k_sort', // 加密规则
            'encrypt_symbol' => '^', // 加密拼接字段
        ];
        // 如果没有数据 抛出异常 应该是没有配置这个支付
    }

    /**
     * 获取支付方法 get|post
     * @return string|boolean
     */
    public function getRequestMethod()
    {
        return strtoupper($this->field[self::REQUEST_METHOD]);
    }

    /**
     * 获取此支付的加密方式
     * @return mixed|string
     */
    public function getEntryType()
    {
        return $this->field[self::ENCRYPT_TYPE];
    }

    /**
     *  获取请求的 url
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->field[self::REQUEST_URL];
    }

}