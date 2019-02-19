<?php

namespace Service\Encrypt;

/**
 * md5 加密方式的请求处理
 * Class Md5
 */
class Md5 extends BaseEncrypt implements EncryptInterface
{

    /**
     * 获取加密处理后的数据
     * @return array|bool
     */
    public function getEncryptPayData()
    {
        // 验证数据库字段
        if (!$this->validateField()) {
            return false;
        }

        // 获取整理后的请求第三方的数据
        $requestData = $this->getRequestDataBySort();
        if (!$requestData) {
            return false;
        }

        return $requestData;
    }

    /**
     * 对请求数据根据请求排序规则排序
     * @return array|boolean
     */
    protected function getRequestDataBySort()
    {
        //  获取参与请求的字段
        $payField = $this->getPayField();
        if (!$payField) {
            return false;
        }
        // 获取加密字段
        $encrypt_field_str = $this->getEncryptField();
        if (!$encrypt_field_str) {
            return false;
        }

        // 合并加密字段
        $payField = array_merge($payField, [$this->field[self::ENCRYPT_FIELD] => md5($encrypt_field_str)]);

        if (isset($this->field['rule']) && $this->field['rule']) {
            if (!in_array($this->field['rule'], self::CONFIG['rule'])) {
                $this->errMessage = '拼接数据的规则不存在';
                return false;
            }
            switch ($this->field['rule']) {
                case 'k_sort': // 按照键升序
                    ksort($payField);
                    break;
            }
        }
        return $payField;
    }


}