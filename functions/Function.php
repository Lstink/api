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
