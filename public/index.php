<?php

define('APP_PATH',__DIR__);
define('FUN_PATH',__DIR__.DIRECTORY_SEPARATOR.'../functions');

include_once '../libs/Autoload.class.php';
// 加载composer提供的自动加载类
require_once "../vendor/autoload.php";

//路由
$routes = new \libs\Route();
list($controller,$action) = $routes -> routeParse();

//实例化控制器
$controller = 'controllers\\'.$controller;
(new $controller()) -> $action();