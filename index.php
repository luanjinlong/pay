<?php

define('BASEDIR', __DIR__);
// 支付 demo
spl_autoload_register(function ($class) {
    require BASEDIR . '/' . str_replace('\\', '/', $class) . '.php';
});

/**
 *  使用的支付名
 */
$pay_name = 'test_pay';

//// 获取支付处理类
$class = new Pay($pay_name);
$pay_result = $class->pay();
if (!$pay_result) {
    var_dump($class->getErrMessage());
} else {
    var_dump($pay_result);
}

