<?php

/**
 * 处理支付
 * Class DealMd5
 */
class Pay extends Controller
{
    /**
     *  加密方式的字段  这个是数据库的字段
     */
    const ENCRY_TYPE = 'encry_type';

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
    private $encry;

    /**
     * 支付名
     * @var string
     */
    private $payName;

    /**
     * 支付对应的字段名
     * @var string
     */
    private $field;

    /**
     *  此支付的加密方式对应的加密类
     * @var \Encry\EncryInterface|\Encry\Md5
     */
    private $handel;

    /**
     * DealMd5 constructor.
     * @param $payName
     */
    public function __construct($payName)
    {
        $this->payName = $payName;
        //  实例化的同时，将数据从数据库中获取
        $this->getField();
    }

    /**
     * 支付
     */
    public function pay()
    {
        if (!$this->handel) {
            if (!$this->getHandelClass()) {
                return false;
            }
        }

        $field = $this->field;
        if (!$field) {
            $this->errMessage = '获取第三方支付字段失败';
            return false;
        }

        $result = $this->handel->setField($field)->pay();

        if (!$result) {
            $this->errMessage = $this->handel->errMessage;
            return false;
        }

        return true;
    }


    /**
     * 从数据库中取出第三方对应的键和相应的值
     * @return array|string
     */
    private function getField()
    {
        if ($this->field) {
            return $this->field;
        }

        //  todo 从数据库中取出一系列的对应第三方字段 此处 demo 我直接假设写数据
        return $this->field = [
            'symbol' => '#',
            'encry_type' => 'md5',
            'request_method' => 'get',
            'rule' => 'sort',
            'request_data' => '{}', // 参与请求的字段，这个是 json 格式
            'request_type' => 'curl', // 请求支付的方式
        ];
        // 如果没有数据 抛出异常 应该是没有配置这个支付
    }

    /**
     * 获取支付加密方式对应的类
     * @return bool|\Encry\EncryInterface|\Encry\Md5|\Encry\Rsa
     */
    private function getHandelClass()
    {
        if (!$this->isSupportEncry()) {
            return false;
        }
//        // 加载相应的加密类文件  并返回实例化
//        $file = $this->encry . '.php';
//        if (!file_exists($file)) {
//            $this->errMessage = '此支付方式对应的加密文件不存在';
//            return false;
//        }
        // 入口 index.php 使用了 spl_autoload_register 这里会自动引入类文件
        switch ($this->encry) {
            case 'md5':
                $this->handel = new \Encry\Md5();
                break;
            case 'rsa':
                $this->handel = new \Encry\Rsa();
                break;
            default:
                $this->errMessage = '没有对应的加密处理类';
                return false;
        }
        return $this->handel;
    }


    /**
     * 获取此支付的加密方式
     * @return mixed|string
     */
    private function getEntryType()
    {
        if ($this->encry) {
            return $this->encry;
        }
        return $this->field[self::ENCRY_TYPE];
    }

    /**
     *  这种加密方式是否支持
     * @return bool
     */
    private function isSupportEncry()
    {
        $this->encry = $this->getEntryType();

        if (array_key_exists($this->encry, self::CONFIG_ENTRY_TYPE)) {
            return true;
        }
        $this->errMessage = '加密方式不支持，支持的加密方式有:' . implode(',', array_keys(self::CONFIG_ENTRY_TYPE));
        return false;
    }

}