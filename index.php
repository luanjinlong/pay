<?php

define('BASEDIR', __DIR__); // 项目根路径

require_once './bootstrap.php';

/**
 *  使用的支付名
 */
$pay_name = 'test_pay';
//// 获取支付处理类
$class = new \Service\Pay();
$pay_result = $class->pay($pay_name);
if (!$pay_result) {
    dd($class->getErrMessage());
} else {
    dd($pay_result);
}
