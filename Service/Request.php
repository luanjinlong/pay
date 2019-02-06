<?php

namespace Service;
/**
 * 请求方式的类
 * Class Request
 */
class Request extends \Controller
{

    /**
     * 请求方式的配置信息
     */
    const REQUEST_METHOD = [
        'get', 'post'
    ];

    /**
     * 请求方法的配置信息
     */
    const REQUEST_TYPE = [
        'curl',
    ];

    /**
     * 请求方式
     * @var string
     */
    private $request_method;

    /**
     * 请求方法
     * @var string
     */
    private $request_type;

    /**
     * 请求数据
     * @var array|string
     */
    private $request_data;

    /**
     * 请求第三方支付
     * @return boolean
     */
    public function pay()
    {
        if (!$this->validate()) {
            return false;
        }

        switch ($this->request_type) {
            case 'curl':
                $response = $this->curl($this->request_data, $this->request_method);
                break;
            default:
                $response = false;
                $this->errMessage = '无效的请求方式';
                break;
        }
        return $response;
    }

    /**
     * 设置请求方式
     * @param $method
     * @return $this
     */
    public function setRequestMethod($method)
    {
        $this->request_method = $method;
        return $this;
    }

    /**
     * 设置请求数据
     * @param $data
     * @return $this
     */
    public function setRequestData($data)
    {
        $this->request_data = $data;
        return $this;
    }

    /**
     * 设置请求方法
     * @param $type
     * @return $this
     */
    public function setRequestType($type)
    {
        $this->request_type = $type;
        return $this;
    }

    /**
     * 请求方式是否符合需求
     * @return bool
     */
    private function isSupportRequestType()
    {
        if (!$this->request_type) {
            $this->errMessage = '请传入请求方式';
            return false;
        }

        if (!in_array($this->request_type, self::REQUEST_TYPE)) {
            $this->errMessage = '请求方式不符合需求';
            return false;
        }
        return true;
    }

    /**
     * 请求方法是否符合需求
     * @return bool
     */
    private function isSupportRequestMethod()
    {
        if (!$this->request_method) {
            $this->errMessage = '请传入请求方法';
            return false;
        }

        if (!in_array($this->request_method, self::REQUEST_METHOD)) {
            $this->errMessage = '请求方法不符合需求';
            return false;
        }
        return true;
    }

    /**
     * curl 请求
     * @param $data array 请求数据
     * @param $method string 请求方法
     * @return bool
     */
    private function curl($data, $method)
    {
        return true;
    }

    /**
     * 请求第三方支付前的验证
     * @return bool
     */
    private function validate()
    {
        if (!$this->isSupportRequestType()) {
            return false;
        }

        if (!$this->isSupportRequestMethod()) {
            return false;
        }

        if (!$this->request_data) {
            $this->errMessage = '请求数据必须填写';
            return false;
        }

        return true;
    }
}