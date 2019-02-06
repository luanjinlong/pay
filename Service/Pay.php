<?php

namespace Service;

use Repository\PayData;

/**
 * 处理支付 这个相当于是 service 层  处理业务逻辑
 * Class DealMd5
 */
class Pay extends \Controller
{
    /**
     * 加密方式的字段  这个是数据库的字段
     */
    const ENCRYPT_TYPE = 'encrypt_type';

    // 加密类型和配置
    const CONFIG_ENTRY_TYPE = [
        // md5 加密配置
        'md5' => 1,
        // rsa 加密配置
        'rsa' => 2, // 在数据库中的数字代码
    ];

    /**
     * 加密方式
     * @var  string
     */
    private $encrypt;

    /**
     * 支付名
     * @var string
     */
    private $payName;

    /**
     *  此支付的加密方式对应的加密类
     * @var \Encrypt\EncryptInterface|\Encrypt\Md5
     */
    private $encryptHandel;

    /**
     * 支付对应的字段名
     * @var string
     */
    private $field;

    /**
     * DealMd5 constructor.
     * @param $payName
     */
    public function __construct($payName)
    {
        $this->payName = $payName;
    }

    /**
     * 支付
     */
    public function pay()
    {
        //  获取请求的最终字段
        $pay_data = $this->getPayData();
        if (!$pay_data) {
            return false;
        }

//
//        // 请求支付的方式 curl 获取其他的之类  这个也在数据库字段里面
//        $request = new \Request();
//
//        $pay = $request->setRequestMethod($this->field['request_method'])
//            ->setRequestData($this->requestData)
//            ->setRequestType($this->field['request_type'])
//            ->pay();


        return true;
    }

    /**
     * 获取请求支付的最终数据
     * @return bool|string
     */
    private function getPayData()
    {
        // 1.获取此支付对应的数据
        $this->field = $this->getPayDataClass()->getFieldBtPayName($this->payName);
        if (!$this->field) {
            $this->errMessage = '没有此支付方式对应的配置';
            return false;
        }

        // 2.获取加密对应的加密类，去处理数据
        if (!$this->encryptHandel) {
            if (!$this->getHandelClassByEncrypt()) {
                return false;
            }
        }

        // 3. 获取在加密类中处理后的最终数据
        $encryptPayData = $this->encryptHandel->setField($this->field)->getEncryptPayData();

        if (!$encryptPayData) {
            $this->errMessage = $this->encryptHandel->getErrMessage();
            return false;
        }
        return $encryptPayData;
    }

    /**
     * 获取支付加密方式对应的类
     * @return bool|\Encrypt\EncryptInterface|\Encrypt\Md5|\Encrypt\Rsa
     */
    private function getHandelClassByEncrypt()
    {
        if (!$this->isSupportEncrypt()) {
            return false;
        }

        switch ($this->encrypt) {
            case 'md5':
                $this->encryptHandel = new \Encrypt\Md5();
                break;
            case 'rsa':
                $this->encryptHandel = new \Encrypt\Rsa();
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
        if ($this->encrypt) {
            return $this->encrypt;
        }
        return $this->encrypt = $this->field[self::ENCRYPT_TYPE];
    }

    /**
     *  这种加密方式是否支持
     * @return bool
     */
    private function isSupportEncrypt()
    {
        $this->encrypt = $this->getEntryType();

        if (array_key_exists($this->encrypt, self::CONFIG_ENTRY_TYPE)) {
            return true;
        }
        $this->errMessage = '加密方式不支持，支持的加密方式有:' . implode(',', array_keys(self::CONFIG_ENTRY_TYPE));
        return false;
    }

    /**
     * 获取支付数据类
     * @return PayData
     */
    private function getPayDataClass()
    {
        static $payData;
        if ($payData) {
            return $payData;
        }
        return $payData = new PayData();
    }

}