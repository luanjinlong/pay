<?php

namespace Service;

use GuzzleHttp\Client;
use Repository\PayData;

/**
 * 处理支付 这个相当于是 service 层  处理业务逻辑
 * Class DealMd5
 */
class Pay extends \Controller
{

    /**
     * @var string 支付对应的字段名
     */
    private $field;

    /**
     * @var string 支付名
     */
    private $payName;

    /**
     * Pay constructor.
     */
    public function __construct()
    {

    }

    /**
     * 设置支付名
     * @param $payName
     * @return $this
     */
    public function setPayName($payName)
    {
        $this->payName = $payName;
        return $this;
    }

    /**
     * 支付中心
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pay()
    {
        if (!$this->payName) {
            $this->errMessage = '请传入支付名';
            return false;
        }
        // 1.获取此支付对应的数据
        $this->field = $this->getPayDataClass()->getFieldByPayName($this->payName);
        //  如果这个支付没有数据库数据，则无法进行人恶化操作，此处抛出异常
        if (!$this->field) {
            $message = $this->getPayDataClass()->getErrMessage();
            payLogger($this->field[PayData::PAY_NAME], $message, $this->field[PayData::PAY_NAME]);
            throwError($message);
        }

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
        $response = $client->request(strtoupper($this->getPayDataClass()->getRequestMethod()), $this->getRequestUrl(), $pay_data);
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
        if (!$this->field[PayData::REQUEST_URL]) {
            $message = '没有配置支付名';
            payLogger($this->field[PayData::PAY_NAME], $message, $this->field[PayData::PAY_NAME]);
            throwError($message);
        }
        return $this->field[PayData::REQUEST_URL];
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
        $encryptHandel = $this->getPayDataClass()->getHandelClassByEncrypt();
        if (!$encryptHandel) {
            return false;
        }

        // 3. 获取在加密类中处理后的最终数据
        $encryptPayData = $encryptHandel->setField($this->field)->getEncryptPayData();
        dd($encryptPayData);
        if (!$encryptPayData) {
            $this->errMessage = $encryptHandel->getErrMessage();
            return false;
        }

        // todo 请求数据sql入库
        return $encryptPayData;
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