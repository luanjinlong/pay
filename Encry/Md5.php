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
        'symbol' => ['%', '#', '^', '&'], // 使用什么符号拼接
        'request_method' => ['get', 'post'],
        'rule' => ['sort'],// 请求字段的拼接规则
    ];

    /**
     * 请求的数据
     * @var array
     */
    private $requestData = [];

    /**
     * 加密拼接符号
     * @var array
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

        // 请求第三方支付
        // 获取整理后的请求第三方的数据
        $this->getRequestDataByRule();
        // 请求支付的方式 curl 获取其他的之类  这个也在数据库字段里面
        $request = new \Request();

        $pay = $request->setRequestMethod($this->field['request_method'])
            ->setRequestData($this->requestData)
            ->setRequestType($this->field['request_type'])
            ->pay();

        if (!$pay) {
            $this->errMessage = $request->errMessage;
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
     * todo  公共的数据在这里验证，但是不是必须的数据，就不要验证了
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
        $symbol = '&';
        if (!in_array($symbol, self::CONFIG['symbol'])) {
            // todo  如果有新的配置符号，记得在 CONFIG 配置中配置
            $this->errMessage = '拼接符号不符合需求';
            return false;
        }

        // 验证请求方式
        if (!in_array($this->field['request_method'], self::CONFIG['request_method'])) {
            $this->errMessage = '请求方式不符合需求';
            return false;
        }

        return true;
    }

    /**
     * 获取加密后的数据
     * @return string
     */
    private function getEncryData()
    {
        return '';
    }

    /**
     * 获取请求支付的键值对数据
     * @return array
     */
    private function getRequestData()
    {
        // todo  测试环境下 默认数据就是数据库数据
        if (DEBUG) {
            return $this->requestData = $this->field;
        }

        foreach ($this->field as $field_name => $pay_name) {
            // post 请求的 name 还是数据库字段名
            if (isset($_POST[$field_name])) {
                $this->requestData[$pay_name] = $_POST[$field_name];
            }
        }
        return $this->requestData;
    }

    /**
     * 对请求数据根绝请求规则整理
     * @return array|boolean
     */
    private function getRequestDataByRule()
    {
        $requestData = $this->getRequestData();
        if (isset($requestData['rule']) && $requestData['rule']) {
            if (!in_array($requestData['rule'], self::CONFIG['rule'])) {
                $this->errMessage = '拼接数据的规则不存在';
                return false;
            }
            switch ($requestData['rule']) {
                case 'sort':
                    sort($this->requestData);
                    break;

            }
        }
        return $this->requestData;
    }

}