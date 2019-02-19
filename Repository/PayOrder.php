<?php

namespace Repository;

/**
 * 支付订单对应的数据处理
 * Class PayOrder
 * @package Repository
 */
class PayOrder extends \Controller
{
    const PAY_WAIT = -1;
    const PAY_LOCK = -2;
    const PAY_FAIL = -3;
    const PAY_SUCCESS = 1;

    /**
     *  订单状态对应的意义
     */
    const ORDER_STATUS = [
        self::PAY_WAIT => '订单已经创建,等待支付',
        self::PAY_LOCK => '订单锁定中(正在支付)',
        self::PAY_FAIL => '订单已请求支付，但支付失败',
        self::PAY_SUCCESS => '订单支付成功',
    ];

    /**
     * 根据订单状态获取状态对应的状态名
     * @param $status
     * @return mixed|string
     */
    public function getStatusNameByStatus($status)
    {
        if (!array_key_exists($status, self::ORDER_STATUS)) {
            return '未知状态';
        }
        return self::ORDER_STATUS[$status];
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
        //  todo  订单入库
        return true;
    }

    /**
     * 根据订单号更新订单状态为锁定
     * @param $order_num int
     * @return bool
     */
    public function updateToLockByOrder($order_num)
    {
        // todo sql 更新状态为锁定
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