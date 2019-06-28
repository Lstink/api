<?php

define('APP_PATH',__DIR__);
define('FUN_PATH',__DIR__.DIRECTORY_SEPARATOR.'../functions');
define('CONFIG_PATH',__DIR__.DIRECTORY_SEPARATOR.'../config');
define('LOG_PATH',__DIR__.DIRECTORY_SEPARATOR.'../logs');

include_once '../libs/Autoload.class.php';
// 加载composer提供的自动加载类
require_once "../vendor/autoload.php";

//关闭错误处理
if (config('config.errorHandler')) {
    // ini_set('error_reporting','On');
    ini_set('display_errors','On');
    // error_reporting(E_ALL);
    // set_exception_handler('a');
    // set_error_handler();
    // register_shutdown_function();
}


//路由
$routes = new \libs\Route();
list($controller,$action) = $routes -> routeParse();



//实例化控制器
$controller = 'controllers\\'.$controller;
(new $controller()) -> $action();