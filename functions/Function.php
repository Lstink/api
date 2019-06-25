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

function encrypt($data,$key='yyy')
{
    return openssl_encrypt($data,'DES-ECB',$key);
}

function decrypt($data,$key='yyy')
{
    return openssl_decrypt($data,'DES-ECB',$key);
}

function encryptCBC($data,$key='yyy',$iv='yyyyyyyy')
{
    return openssl_encrypt($data,'DES-CBC',$key,0,$iv);
}

function decryptCBC($data,$key='yyy',$iv='yyyyyyyy')
{
    return openssl_decrypt($data,'DES-CBC',$key,0,$iv);
}

function writeLog($data,$level)
{
    return \libs\Log::getInstance() -> writeLog($data,$level);
}

function monolog($data,$name,$level='INFO')
{
    return \libs\Log::getInstance() -> monolog($data,$name,$level);
}