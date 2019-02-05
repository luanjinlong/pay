<?php


class Controller
{
    /**
     * 报错信息
     * @var string
     */
    public $errMessage;


    /**
     * 虎哦去报错
     * @return string
     */
    public function getErrMessage()
    {
        if ($this->errMessage) {
            return $this->errMessage;
        }
        return '没有报错';
    }
}