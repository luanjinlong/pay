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
    const Encrypt_TYPE = 'Encrypt_type';

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
        // 1.获取此支付对应的数据
        $this->getPayData()->getFieldBtPayName($this->payName);
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

        // 3. 在加密类中处理数据
        $result = $this->encryptHandel->setField($this->field)->pay();

        if (!$result) {
            $this->errMessage = $this->encryptHandel->errMessage;
            return false;
        }

        return true;
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
//        // 加载相应的加密类文件  并返回实例化
//        $file = $this->encrypt . '.php';
//        if (!file_exists($file)) {
//            $this->errMessage = '此支付方式对应的加密文件不存在';
//            return false;
//        }
        // 入口 index.php 使用了 spl_autoload_register 这里会自动引入类文件
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
        return $this->encrypt = $this->field[self::Encrypt_TYPE];
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
    private function getPayData()
    {
        static $payData;
        if ($payData) {
            return $payData;
        }
        return $payData = new PayData();
    }

}