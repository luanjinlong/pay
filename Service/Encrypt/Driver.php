<?php

namespace Service\Encrypt;

/**
 * 工厂模式 获取对应的加密类
 * Class Driver
 * @package Service\Encrypt
 */
class Driver extends \Controller
{

    // 加密类型和配置
    const CONFIG_ENTRY_TYPE = [
        // md5 加密配置
        'md5' => 1,
        // rsa 加密配置
        'rsa' => 2, // 在数据库中的数字代码
        // 函数在文件中存储
        'file' => 3,
    ];


    /**
     * 获取支付加密方式对应的类
     * @param $encrypt
     * @return bool|File|Md5|Rsa
     */
    public function getHandleByEncrypt($encrypt)
    {
        if (!$this->isSupportEncrypt($encrypt)) {
            return false;
        }

        switch ($encrypt) {
            case 'md5':
                $encryptHandel = new Md5();
                break;
            case 'rsa':
                $encryptHandel = new Rsa();
                break;
            case 'file': // 第三方自定义的加密规则
                $encryptHandel = new File();
                break;
            default:
                $this->errMessage = $encrypt . '加密，没有对应的加密处理类';
                return false;
        }
        return $encryptHandel;
    }

    /**
     *  这种加密方式是否支持
     * @param $encrypt
     * @return bool
     */
    private function isSupportEncrypt($encrypt)
    {
        if (array_key_exists($encrypt, self::CONFIG_ENTRY_TYPE)) {
            return true;
        }
        $this->errMessage = $encrypt . '加密方式不支持，支持的加密方式有:' . implode(',', array_keys(self::CONFIG_ENTRY_TYPE));
        return false;
    }


}