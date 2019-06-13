<?php


function demo()
{
    echo 'This is a demo';
}
function createSign($data=[])
{
    //生成随机数
    $randStr= createRandString();
    //时间戳
    $timestamp = time();
    //key
    $key = '1810B';
    $signArr = [$timestamp,$randStr,$key];
    $signArr = $signArr + $data;
    sort($signArr,SORT_STRING);
    $signature = sha1(implode($signArr));
    return ['nonceStr'=>$randStr,'timestamp'=>$timestamp,'signature'=>$signature];

}
function createRandString($length = 20)
{
    $string = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM@$%';
    $string = str_shuffle($string);
    return substr($string,1,$length);
}