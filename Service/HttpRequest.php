<?php

namespace Service;

use GuzzleHttp\Client;
use Repository\PayData;
use Repository\PayOrder;

/**
 * 请求类
 * Class HttpRequest
 * @package Service
 */
class HttpRequest extends \Controller
{

    /**
     * @return Client
     */
    private function getGuzzle()
    {
        static $client;
        return isset($client) ? $client : $client = new Client();
    }

    /**
     * 请求支付
     * @param $pay_data
     * @param $url
     * @param $request_method
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($pay_data, $url, $request_method)
    {
        if ($pay_data) {
            if ($request_method == 'POST') {
                $pay_data = [
                    'form_params' => $pay_data, // post 请求的参数需要以 form_params 为键
                ];
            } elseif ($request_method == 'GET') {
                $pay_data = [
                    'query' => http_build_query($pay_data), // post 请求的参数需要以 form_params 为键
                ];
            } else {
                $this->errMessage = '请传入请求方式';
                return false;
            }
        }

        // 更改订单号为锁定中
        $this->getPayOrderRepository()->updateToLockByOrder($pay_data[PayData::ORDER_NUM]);

        $client = $this->getGuzzle();
        $response = $client->request($request_method, $url, $pay_data);
        if ($response->getStatusCode() == 200) {
            return true;
        } else {
            $this->errMessage = $response->getBody()->getContents();
            return false;
        }
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
