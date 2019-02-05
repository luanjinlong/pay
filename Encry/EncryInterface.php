<?php

namespace Encry;
/**
 * 加密类的接口
 * Interface EncryInterface
 */
interface EncryInterface
{
    /**
     * 设置第三方对应的支付字段  程序中需要在加密类处理数据，所以需要预留一个入口接收数据
     * @return $this
     */
    function setField($field);

    /**
     * 请求支付
     * @return string
     */
    function pay();

    /**
     * 同步回掉
     * @return mixed
     */
    function notify();

    /**
     * 一部回掉
     * @return mixed
     */
    function sync();

}