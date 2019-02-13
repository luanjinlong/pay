<?php

/**
 *  这个页面其实也相当于是路由走到了控制器的页面
 */
define('BASEDIR', __DIR__); // 项目根路径
define('DEBUG', true); // 调试模式
error_reporting(0);
require BASEDIR . '/vendor/autoload.php';
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


// 支付 demo
spl_autoload_register(function ($class) {
    require BASEDIR . '/' . str_replace('\\', '/', $class) . '.php';
});

require 'Common/function.php';
//
//$logger = new \Monolog\Logger('test');
//$logger->pushHandler(new \Monolog\Handler\StreamHandler(BASEDIR . '/storage/logs/test.log', \Monolog\Logger::DEBUG));
//$logger->info(2444);
//dd($logger);

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

