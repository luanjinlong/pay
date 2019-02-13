<?php

namespace Service;

use GuzzleHttp\Client;
use Repository\PayData;
use Service\Encrypt\Md5;
use Service\Encrypt\Rsa;

/**
 * 处理支付 这个相当于是 service 层  处理业务逻辑
 * Class DealMd5
 */
class Pay extends \Controller
{
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
     * 请求方式的配置信息
     */
    const REQUEST_METHOD = [
        'get', 'post'
    ];

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
     * 请求方式
     * @var string
     */
    private $request_method = 'GET';

    /**
     * 加密方式
     * @var  string
     */
    private $encrypt;

    /**
     *  此支付的加密方式对应的加密类
     * @var \Service\Encrypt\Md5|\Service\Encrypt\Rsa|\Service\Encrypt\EncryptInterface
     */
    private $encryptHandel;

    /**
     * 支付对应的字段名
     * @var string
     */
    private $field;

    /**
     * Pay constructor.
     * @param $payName
     * @throws \Exception
     */
    public function __construct($payName)
    {
        // 1.获取此支付对应的数据
        $this->field = $this->getPayDataClass()->getFieldBtPayName($payName);
        //  如果这个支付没有数据库数据，则无法进行人恶化操作，此处抛出异常
        if (!$this->field) {
            $message = $this->getPayDataClass()->getErrMessage();
            payLogger($this->field[self::PAY_NAME], $message, $this->field[self::PAY_NAME]);
            throwError($message);
        }
    }

    /**
     * 支付中心
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pay()
    {
        //  获取请求的最终字段
        $pay_data = $this->getPayData();
        if (!$pay_data) {
            return false;
        }

        // 请求支付
        $pay = $this->request($pay_data);

        if (!$pay) {
            $this->payFail();
            return false;
        }
        $this->paySuccess();
        return true;
    }

    /**
     * 请求支付
     * @param $pay_data array
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request($pay_data)
    {
        $client = $this->getGuzzle();
        $response = $client->request(strtoupper($this->request_method), $this->getRequestUrl(), $pay_data);
        if ($response->getStatusCode() == 200) {
            return true;
        } else {
            $this->errMessage = $response->getBody()->getContents();
            return false;
        }
    }

    /**
     *  获取请求的 url
     * @return string
     * @throws \Exception
     */
    private function getRequestUrl()
    {
        if (!$this->field[self::REQUEST_URL]) {
            $message = '没有配置支付名';
            payLogger($this->field[self::PAY_NAME], $message, $this->field[self::PAY_NAME]);
            throwError($message);
        }
        return $this->field[self::REQUEST_URL];
    }

    /**
     * todo 支付失败处理
     * @return bool
     */
    private function payFail()
    {
        return true;
    }

    /**
     * todo 支付成功处理
     * @return bool
     */
    private function paySuccess()
    {
        return true;
    }

    /**
     * 获取请求支付的最终数据
     * @return bool|string
     */
    private function getPayData()
    {
        // 2.获取加密对应的加密类，去处理数据
        $encryptHandel = $this->getHandelClassByEncrypt();
        if (!$encryptHandel) {
            return false;
        }

        // 3. 获取在加密类中处理后的最终数据
        $encryptPayData = $this->encryptHandel->setField($this->field)->getEncryptPayData();
        if (!$encryptPayData) {
            $this->errMessage = $this->encryptHandel->getErrMessage();
            return false;
        }

        // todo 请求数据sql入库
        return $encryptPayData;
    }

    /**
     * 获取支付加密方式对应的类
     * @return bool|Encrypt\EncryptInterface|Md5|Rsa
     */
    private function getHandelClassByEncrypt()
    {
        if ($this->encryptHandel) {
            return $this->encryptHandel;
        }

        if (!$this->isSupportEncrypt()) {
            return false;
        }

        switch ($this->encrypt) {
            case 'md5':
                $this->encryptHandel = new Md5();
                break;
            case 'rsa':
                $this->encryptHandel = new Rsa();
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


    /**
     * @return Client
     */
    private function getGuzzle()
    {
        static $client;
        if ($client) {
            return $client;
        }
        return $client = new Client();
    }


}