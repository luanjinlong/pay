<?php

namespace Encry;

/**
 * md5 加密方式的请求处理
 * Class Md5
 */
class Md5 extends \Controller implements EncryInterface
{
    /**
     * md5方式加密配置
     */
    const CONFIG = [
        'sql_type' => 1, // 在数据库中的数字代码
        'symbol' => ['%', '#', '^'], // 使用什么符号拼接
    ];
    /**
     * 加密拼接符号
     * @var string
     */
    private $field;

    /**
     * 设置第三方对应的支付字段
     * @param $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * 请求支付
     * @return bool|string
     */
    public function pay()
    {
        // 获取字段
        if (!$this->field) {
            $this->errMessage = '请传入第三方的支付字段';
            return false;
        }
        // 验证字段
        if (!$this->validateField()) {
            return false;
        }

        // 请求支付的方式 curl 获取其他的之类  这个也在数据库字段里面
        $requestType = 'curl';
        $request = new \Request();

        // todo 这里需要好好设计  也就是
        if (!$request->isSupport($requestType)) {
            return false;
        }

        $pay = call_user_func([$request, $requestType]);
        if (!$pay) {
            $this->errMessage = '请求支付失败，此处会返回第三方信息';
            return false;
        }
        return true;

    }

    public function notify()
    {

    }

    public function sync()
    {

    }

    /**
     * 验证第三方支付字段
     * @return bool
     */
    private function validateField()
    {
        if (!$this->field) {
            $this->errMessage = '请传入第三方的支付字段';
            return false;
        }

        // todo 每一个重要的字段都要验证  第三方的字段就是数据表字段对应的值
        //  验证方式  比如
        $money = 'payMoney';
        if ($money == '') {  // 验证的时候，只比较字段是不是等于默认值即可，如果是默认值，表示此字段没有配置
            $this->errMessage = '金额字段没有配置第三方字段';
            return false;
        }
        // todo 还有一些额外验证的字段  需要跟本地的配置比较 比如拼接符号
        $symbol = '#';
        if (!in_array($symbol, self::CONFIG['symbol'])) {
            // todo  如果有新的配置符号，记得在 CONFIG 配置中配置
            $this->errMessage = '拼接符号不符合需求';
            return false;
        }
        return true;
    }

}