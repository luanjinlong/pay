<?php

define('BASEDIR', __DIR__); // 项目根路径

require 'bootstrap.php';

/**
 *  使用的支付名
 */
$pay_name = 'test_pay';

//// 获取支付处理类
$class = new \Service\Pay();
$pay_result = $class->setPayName($pay_name)->pay();
if (!$pay_result) {
    dd($class->getErrMessage());
} else {
    dd($pay_result);
}

// 还原成之前的异常处理程序
restore_exception_handler();