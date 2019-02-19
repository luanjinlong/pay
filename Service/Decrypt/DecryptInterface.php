<?php

namespace Service\Decrypt;
/**
 * 解密类的接口
 * Interface EncryptInterface
 */
interface DecryptInterface
{

    /**
     * 设置第三方对应的支付字段
     * @param $field
     * @return $this
     */
    public function setField($field);

    /**
     * 获取加密处理后的数据
     * @return bool|array
     */
    public function getEncryptPayData();

}