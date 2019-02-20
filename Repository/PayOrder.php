<?php

namespace Repository;

/**
 * 支付订单对应的数据处理
 * Class PayOrder
 * @package Repository
 */
class PayOrder extends \Controller
{
    const ORDER_CREATE = -1;
    const ORDER_LOCK = -2;
    const ORDER_REQUEST_FAIL = -3;
    const ORDER_FAIL = -4;
    const ORDER_SUCCESS = 1;

    /**
     *  订单状态对应的意义
     */
    const STATUS_ORDER = [
        self::ORDER_CREATE => '新订单', // 支付之前创建订单
        self::ORDER_LOCK => '锁定中(正在支付)', // 发送请求成功，等待回掉
        self::ORDER_REQUEST_FAIL => '请求支付失败', // 请求支付失败，request 请求出现问题
        self::ORDER_FAIL => '支付失败', // 回掉支付失败
        self::ORDER_SUCCESS => '订单支付成功', // 回掉支付成功
    ];

    /**
     * 根据订单状态获取状态对应的状态名
     * @param $status
     * @return mixed|string
     */
    public function getStatusNameByStatus($status)
    {
        if (!array_key_exists($status, self::STATUS_ORDER)) {
            return '未知状态';
        }
        return self::STATUS_ORDER[$status];
    }

    /**
     * todo 根据订单号获取对应的状态名
     * @param $order
     * @return mixed|string
     */
    public function getStatusNameByOrderNum($order)
    {
        $sql = [];
        return $this->getStatusNameByStatus($sql[PayData::ORDER_NUM]);
    }

    /**
     * 创建订单
     * @param $arr
     * @return bool
     */
    public function createOrder($arr)
    {
        $order = $arr[PayData::ORDER_NUM];
        //  todo  订单入库 self::ORDER_CREATE
        return true;
    }

    /**
     * 根据订单号更新订单状态为锁定 已经发送请求成功  等待回掉
     * @param $order_num int
     * @return bool
     */
    public function updateToLockByOrder($order_num)
    {
        // todo sql 更新状态为锁定
        return true;
    }

    /**
     * 订单号更新为请求支付失败
     * @param $order_num int
     * @return bool
     */
    public function updateToRequestFailByOrder($order_num)
    {
        // todo sql 更新状态为锁定  self::ORDER_REQUEST_FAIL
        return true;
    }

    /**
     * 根据订单号更新订单状态为支付失败
     * @param $order_num int
     * @return bool
     */
    public function updateToFailByOrder($order_num)
    {
        // todo sql 更新状态为支付失败
        return true;
    }

    /**
     * 根据订单号更新订单状态为支付成功
     * @param $order_num int
     * @return bool
     */
    public function updateToSuccessByOrder($order_num)
    {
        // todo sql 更新状态为支付成功
        return true;
    }

}