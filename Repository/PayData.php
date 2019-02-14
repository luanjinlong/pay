<?php

namespace Repository;

use Service\Encrypt\File;
use Service\Encrypt\Md5;
use Service\Encrypt\Rsa;

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

    // 加密类型和配置
    const CONFIG_ENTRY_TYPE = [
        // md5 加密配置
        'md5' => 1,
        // rsa 加密配置
        'rsa' => 2, // 在数据库中的数字代码
    ];

    /**
     *  此支付的加密方式对应的加密类
     * @var \Service\Encrypt\Md5|\Service\Encrypt\Rsa|\Service\Encrypt\EncryptInterface
     */
    private $encryptHandel;

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
            'request_method' => 'post',
            'rule' => 'k_sort',
            'request_field' => 'post,money,encrypt', // 参与请求的字段，逗号分隔的字符串
            'request_url' => 'http://www.baidu.com', //请求的地址
            'async_url' => '', //异步回掉地址
            'callback_url' => '', //同步回掉地址
            'pay_money' => 'money',
            'encrypt_type' => 'md5', // 加密方式
            'encrypt_data' => 'post,money,encrypt', // 参与加密的字段，逗号分隔的字符串
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
        return $this->field[self::REQUEST_METHOD];
    }

    /**
     * 获取支付加密方式对应的类
     * @return bool|\Service\Encrypt\EncryptInterface|File|Md5|Rsa
     */
    public function getHandelClassByEncrypt()
    {
        if ($this->encryptHandel) {
            return $this->encryptHandel;
        }

        if (!$this->isSupportEncrypt()) {
            return false;
        }

        switch ($this->getEntryType()) {
            case 'md5':
                $this->encryptHandel = new Md5();
                break;
            case 'rsa':
                $this->encryptHandel = new Rsa();
                break;
            case 'file': // 第三方自定义的加密规则
                $this->encryptHandel = new File();
                break;

            default:
                $this->errMessage = '没有对应的加密处理类';
                return false;
        }
        return $this->encryptHandel;
    }


    /**
     * 获取此支付的加密方式
     * @return mixed|string
     */
    private function getEntryType()
    {
        return $this->field[PayData::ENCRYPT_TYPE];
    }

    /**
     *  这种加密方式是否支持
     * @return bool
     */
    private function isSupportEncrypt()
    {
        if (array_key_exists($this->getEntryType(), PayData::CONFIG_ENTRY_TYPE)) {
            return true;
        }
        $this->errMessage = '加密方式不支持，支持的加密方式有:' . implode(',', array_keys(PayData::CONFIG_ENTRY_TYPE));
        return false;
    }


}