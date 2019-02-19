<?php

namespace Service;

use Repository\PayOrder;
use Repository\PayData;
use Service\Encrypt\Driver;

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
     * 订单号
     * @var int
     */
    private $order_num;

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
        //1. 获取支付对应的配置
        if (!$this->getField()) {
            return false;
        }

        // 2.获取加密方式对应的加密处理类
        $encryptHandel = $this->getEncryptClass()->getHandleByEncrypt($this->getPayDataRepository()->getEntryType());
        if (!$encryptHandel) {
            $this->errMessage = $this->getEncryptClass()->getErrMessage();
            return false;
        }

        // 3. 获取在加密类中处理后的最终数据
        $encryptPayData = $encryptHandel->setField($this->field)->getEncryptPayData();
        if (!$encryptPayData) {
            $this->errMessage = $encryptHandel->getErrMessage();
            return false;
        }

        // 4. 支付之前创建订单
        $createOrder = $this->getPayOrderRepository()->createOrder($encryptPayData);
        if (!$createOrder) {
            $this->errMessage = $this->getPayOrderRepository()->getErrMessage();
            return false;
        }

        //  订单号
        $this->order_num = $encryptPayData[PayData::ORDER_NUM];
        // 请求支付
        $payResult = $this->getHttpRequestClass()->request($encryptPayData, $this->getPayDataRepository()->getRequestUrl(), $this->getPayDataRepository()->getRequestMethod());
        //  z这个只是请求是否成功，回掉才是是否支付成功
        if (!$payResult) {
            return false;
        }

        // 更改订单号为锁定中
        $this->getPayOrderRepository()->updateToLockByOrder($this->order_num);

        return true;
    }

    /**
     * todo 支付回掉处理
     * @return bool
     */
    public function callBack()
    {
        //1. 获取支付对应的配置
        if (!$this->getField()) {
            return false;
        }

        $pay = 1;
        //  z这个只是请求是否成功，回掉才是是否支付成功
        if (!$pay) {
            $this->payFail();
            return false;
        }
        $this->paySuccess();
        return true;
    }


    /**
     * 获取支付名对应的数据库配置
     * @return array|bool|string
     */
    private function getField()
    {
        if (!$this->payName) {
            $this->errMessage = '请传入支付名';
            return false;
        }

        // 1.获取此支付对应的数据
        $this->field = $this->getPayDataRepository()->getFieldByPayName($this->payName);
        //  如果这个支付没有数据库数据，则无法进行人恶化操作，此处抛出异常
        if (!$this->field) {
            $this->errMessage = $this->getPayDataRepository()->getErrMessage();
            return false;
        }
        return $this->field;
    }

    /**
     * todo 支付失败处理
     * @return bool
     */
    private function payFail()
    {
        $this->getPayOrderRepository()->updateToFailByOrder($this->order_num);
        return true;
    }

    /**
     * todo 支付成功处理
     * @return bool
     */
    private function paySuccess()
    {
        $this->getPayOrderRepository()->updateToSuccessByOrder($this->order_num);
        return true;
    }

    /**
     * @return PayData
     */
    private function getPayDataRepository()
    {
        static $payData;
        return isset($payData) ? $payData : $payData = new PayData();
    }

    /**
     * 获取 Request 请求类
     * @return HttpRequest
     */
    private function getHttpRequestClass()
    {
        static $request;
        return isset($request) ? $request : $request = new HttpRequest();
    }

    /**
     * 获取加密方式对应的类
     * @return Driver
     */
    private function getEncryptClass()
    {
        static $encrypt;
        return isset($encrypt) ? $encrypt : $encrypt = new Driver();
    }

    /**
     * 获取订单仓库
     * @return PayOrder
     */
    private function getPayOrderRepository()
    {
        static $repository;
        return isset($repository) ? $repository : $repository = new PayOrder();
    }

}