<?php

/**
 *  这个页面其实也相当于是路由走到了控制器的页面
 */
define('DEBUG', true); // 调试模式
error_reporting(0);

// 引入第三方组件
require BASEDIR . '/vendor/autoload.php';
//
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


if (!DEBUG) {
// 注册异常程序处理
    set_exception_handler(function (Exception $exception) {
        //  如果开启了 debug 则，直接是使用到 whoop 组件，这里不需要打印
        if (!DEBUG) {
            // 揩油开启 debug 则记录错误
            logger(date('Y-m-d'))->debug($exception->getMessage(), [
                'time' => date('Y-m-d H:i:s'),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'track' => $exception->getTraceAsString(),
            ]);
        } else {
            dd($exception->getMessage());
        }
    });
}




