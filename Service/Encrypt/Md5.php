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


}