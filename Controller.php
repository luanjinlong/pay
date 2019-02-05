<?php


class Controller
{
    /**
     * 报错信息
     * @var string
     */
    public $errMessage;


    /**
     * 获取报错
     * @return string
     */
    public function getErrMessage()
    {
        if ($this->errMessage) {
            return $this->errMessage;
        }
        return '没有错误信息';
    }
}