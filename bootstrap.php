<?php

/**
 *  这个页面其实也相当于是路由走到了控制器的页面
 */
define('DEBUG', true); // 调试模式
error_reporting(0);

// 引入第三方组件
require BASEDIR . '/vendor/autoload.php';

if (DEBUG) {
    // 打开 debug 调试
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}


// 引入项目文件
spl_autoload_register(function ($class) {
    require BASEDIR . '/' . str_replace('\\', '/', $class) . '.php';
});

// 引入公共函数
require 'Common/function.php';


