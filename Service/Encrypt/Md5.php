<?php

namespace Service\Encrypt;

/**
 * md5 加密方式的请求处理
 * Class Md5
 */
class Md5 extends \Controller implements EncryptInterface
{
    /**
     * md5方式加密配置
     */
    const CONFIG = [
        'sql_type' => 1, // 在数据库中的数字代码
        'symbol' => ['%', '#', '^', '&'], // 使用什么符号拼接
        'rule' => ['k_sort'],// 请求字段的拼接规则
        'encrypt_rule' => ['k_sort'], // 加密规则的配置
    ];

    /**
     *  数据库的加密字段-- 这个是数据表中的字段 也可以直接使用  但是写在这里方便调试
     */
    const ENCRYPT_FIELD = 'encrypt_field';

    // 参与加密的字段
    const ENCRYPT_DATA = 'encrypt_data';
    // 参与加密规则的字段
    const ENCRYPT_RULE = 'encrypt_rule';

    /**
     * 请求支付的字段--在数据库中获取，这个字段是将加密字段存入逗号分割的字符串
     */
    const REQUEST_FIELD = 'request_field';

    /**
     * 数据库原数据
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
     * 获取加密处理后的数据
     * @return bool|string
     */
    public function getEncryptPayData()
    {
        // 验证数据库字段
        if (!$this->validateField()) {
            return false;
        }

        // 请求第三方支付
        // 获取整理后的请求第三方的数据
        $requestData = $this->getRequestDataBySort();
        if (!$requestData) {
            return false;
        }

        return $requestData;
    }

    /**
     * todo  公共的数据在这里验证，但是不是必须的数据，就不要验证了
     * 验证数据，比如金额是否填写，金额有没有限制额度
     * 必要的其他字段有没有填写
     * 规则验证是否符合此类的 CONFIG 配置
     *
     * 验证第三方支付字段
     * @return bool
     */
    private function validateField()
    {
        // 获取数据库字段
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

        return true;
    }

    /**
     * 获取请求支付的键值对数据  原本是表字段对应的值，这一步整理成表字段值对应的POST值，因为表字段值才是第三方的请求字段
     * @return array|boolean
     */
    private function getPayField()
    {
        // todo  测试环境下 默认数据就是数据库数据
        if (DEBUG) {
            $_POST = $this->field;
        }

        $payData = [];
        $requestFields = explode(',', $this->field[self::REQUEST_FIELD]);
        foreach ($this->field as $field_name => $pay_name) {
            // post 请求的 name 还是数据库字段名
            if (DEBUG) {
                if (in_array($pay_name, $requestFields)) {
                    $payData[$pay_name] = $_POST[$field_name];
                }
            } else {
                if (isset($_POST[$field_name]) && in_array($pay_name, $requestFields)) {
                    $payData[$pay_name] = $_POST[$field_name];
                }
            }
        }

        if (count($requestFields) != count($payData)) {
            $this->errMessage = '请求字段赋值不整完，参与请求的字段有' . $this->field[self::REQUEST_FIELD] . '赋值的字段有:' . implode(',', $payData);
            return false;
        }
        return $payData;
    }

    /**
     * 对请求数据根据请求排序规则排序
     * @return array|boolean
     */
    private function getRequestDataBySort()
    {
        //  获取参与请求的字段
        $payField = $this->getPayField();
        if (!$payField) {
            return false;
        }

        // 获取加密字段
        $encryptField = $this->getEncryptField();
        if (!$encryptField) {
            return false;
        }
        // 合并加密字段
        $payField = array_merge($payField, $encryptField);

        if (isset($payField['rule']) && $payField['rule']) {
            if (!in_array($payField['rule'], self::CONFIG['rule'])) {
                $this->errMessage = '拼接数据的规则不存在';
                return false;
            }
            switch ($payField['rule']) {
                case 'k_sort': // 按照键升序
                    ksort($payField);
                    break;

            }
        }
        return $payField;
    }

    /**
     * 获取加密后的加密字段键值对
     * @return array|boolean
     */
    private function getEncryptField()
    {
        $encryptField = $this->field[self::ENCRYPT_FIELD];
        if (!$encryptField) {
            $this->errMessage = '加密字段没有配置';
            return false;
        }

        $encryptData = $this->field[self::ENCRYPT_DATA];
        if (!$encryptData) {
            $this->errMessage = '参与加密字段没有配置';
            return false;
        }

        if (!$this->isSupportEncryptRule()) {
            return false;
        }

        switch ($this->field[self::ENCRYPT_RULE]) {
            case 'k_sort': // 按照键升序
                ksort($this->field);
                break;
        }


        // todo  要通过加密规则获取加密数据
        return [$encryptField => microtime()];
    }

    /**
     * 验证加密规则是否支持
     * @return bool
     */
    private function isSupportEncryptRule()
    {
        $encryptRule = $this->field[self::ENCRYPT_RULE];

        if (!$encryptRule) {
            $this->errMessage = '参与加密规则的字段没有配置';
            return false;
        }

        if (!in_array($encryptRule, self::CONFIG['encrypt_rule'])) {
            $this->errMessage = '加密规则不支持';
            return false;
        }

        return true;
    }
}