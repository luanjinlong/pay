<?php

define('BASEDIR', __DIR__); // 项目根路径

require 'bootstrap.php';

/**
 *  使用的支付名
 */
$pay_name = 'test_pay';

//// 获取支付处理类
$class = new \Service\Pay($pay_name);
$pay_result = $class->pay();
if (!$pay_result) {
    var_dump($class->getErrMessage());
} else {
    var_dump($pay_result);
}

