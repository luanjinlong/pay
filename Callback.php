<?php

define('BASEDIR', __DIR__); // 项目根路径

require_once './bootstrap.php';

/**
 *  使用的支付名
 */
$pay_name = 'test_pay';
//  异步回掉的处理
$class = new \Service\Pay();
$pay_result = $class->callBack($pay_name);
if (!$pay_result) {
    dd($class->getErrMessage());
} else {
    dd($pay_result);
}



