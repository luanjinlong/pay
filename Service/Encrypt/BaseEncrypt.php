<?php

namespace Service\Encrypt;

/**
 * md5 加密方式的请求处理
 * Class Md5
 */
class BaseEncrypt extends \Controller
{
    /**
     * md5方式加密配置
     */
    const CONFIG = [
        'sql_type' => 1, // 在数据库中的数字代码
        'symbol' => ['%', '#', '^', '&', '^'], // 使用什么符号拼接
        'rule' => ['k_sort'],// 请求字段的拼接规则
        'encrypt_rule' => ['k_sort'], // 加密规则的配置
        'encrypt_symbol' => ['%', '#', '^', '&', '^'],  // 加密规则的配置
    ];


    //  数据库的加密字段-- 这个是数据表中的字段 也可以直接使用  但是写在这里方便调试
    const ENCRYPT_FIELD = 'encrypt_field';
    //  加密方式 md5 rsa
    const ENCRYPT_TYPE = 'encrypt_type';
    // 参与加密的字段
    const ENCRYPT_DATA = 'encrypt_data';
    // 参与加密规则的字段
    const ENCRYPT_RULE = 'encrypt_rule';
    // 加密拼接字段
    const ENCRYPT_SYMBOL = 'encrypt_symbol';

    /**
     * 请求支付的字段--在数据库中获取，这个字段是将加密字段存入逗号分割的字符串
     */
    const REQUEST_FIELD = 'request_field';

    /**
     * 数据库原数据
     * @var array
     */
    protected $field;

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
     * 获取订单号
     * @return string
     */
    protected function getOrderNum()
    {
        $order = date('YmdHis') . mt_rand(1000, 9999);
        // todo  查询订单号是否存在，存在则再换一个
        $sql = false;
        if (!$sql) {
            return $order;
        }
        $this->getOrderNum();
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
    protected function validateField()
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
     * todo 最后别忘了订单号是单独生成处理的
     * 获取请求支付的键值对数据  原本是表字段对应的值，这一步整理成表字段值对应的POST值，因为表字段值才是第三方的请求字段
     * @return array|boolean
     */
    protected function getPayField()
    {
        static $payData;
        if ($payData) {
            return $payData;
        }
        // todo  测试环境下 默认数据就是数据库数据
        if (DEBUG) {
            $_POST = array_merge($this->field, ['order_num' => $this->getOrderNum()]);
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
        return array_merge($payData, ['order_num' => $this->getOrderNum()]);
    }

    /**
     * 获取加密后的加密字段键值对
     * @return  string|boolean
     */
    protected function getEncryptField()
    {
        if (!$this->validateEncryptField()) {
            return false;
        }

        $arr = explode(',', $this->field[self::ENCRYPT_DATA]);
        $encryptData = [];
        $payField = $this->getPayField();
        if (!$payField) {
            return false;
        }
        // 获取验证字段对应的值 键值对
        foreach ($arr as $value) {
            $encryptData[$value] = $payField[$value];
        }

        // 加密数据的排序规则
        switch ($this->field[self::ENCRYPT_RULE]) {
            case 'k_sort': // 按照键升序
                ksort($encryptData);
                break;
        }
        if (!$this->isSupportEncryptSymbol()) {
            return false;
        }

        // 将加密数据拼接成字符串
        $encrypt_field_str = http_build_query($encryptData);
        // todo 不知道这种方式会不会有问题，http_build_query 函数默认是使用 & 符号拼接的 如果字符串中包含有这个函数就会出问题 其实也可以写函数处理这个拼接
        $encrypt_field_str = str_replace('&', $this->field[self::ENCRYPT_SYMBOL], $encrypt_field_str);

        // todo  要通过加密规则获取加密数据
        return $encrypt_field_str;
    }

    /**
     * 验证加密拼接字段是否支持
     * @return bool
     */
    private function isSupportEncryptSymbol()
    {
        $encryptSymbol = $this->field[self::ENCRYPT_SYMBOL];
        if (!$encryptSymbol) {
            $this->errMessage = '参与加密拼接字段没有配置';
            return false;
        }

        if (!in_array($encryptSymbol, self::CONFIG[self::ENCRYPT_SYMBOL])) {
            $this->errMessage = '加密拼接字段不支持';
            return false;
        }
        return true;
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

    /**
     * 验证加密字段
     * @return bool
     */
    private function validateEncryptField()
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
        //  加密规则是否支持
        if (!$this->isSupportEncryptRule()) {
            return false;
        }
        return true;
    }

}