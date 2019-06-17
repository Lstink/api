<?php

function dd($var)
{
    var_dump($var);
    exit;
}

function dump($var)
{
    var_dump($var);
}
//数据的请求
function request()
{
    static $request = null;
    if (!$request instanceof \libs\Request) {
        $request = new \libs\Request;
    }
    return $request;
}

function createRandString($length = 20)
{
    $string = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM@$%';
    $string = str_shuffle($string);
    return substr($string,1,$length);
}

function config($name='')
{
    $config =  \libs\Config::getInstance();
    $res = $config -> getConfig($name);
    return $res;
}