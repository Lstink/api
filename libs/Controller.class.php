<?php

namespace libs;

class Controller
{
    protected $nonceStr;
    protected $timestamps;
    protected $key = '1810B';
    protected $signature;
    /**
     * @content 构造方法
     */
    public function __construct()
    {
        $this -> getSign();
    }
    /**
     * @content 验证签名
     */
    public function checkSign()
    {
        //验证数据 
        $all = request() -> except(['signature','c','a','access_token']);
        $all['key'] = $this -> key;
        //字典排序 -> 索引数组 首字母排序
        sort($all,SORT_STRING);
        $signature = sha1(implode($all));

        if ($this -> signature !== $signature) {
            Response::returnData(1001,'invalid signature');
        }
    }
    /**
     * @content get签名认证
     */
    public function getSign()
    {
        //获得所有的参数
        $all = request() -> all();
        //获取随机数
        $nonceStr = request() -> only('nonceStr');
        //获取时间戳
        $timestamp = request() -> only('timestamp');
        //获取客户端传递过来的签名
        $signature = request() -> only('signature');

        if (empty($nonceStr) || empty($timestamp) || empty($signature)) {
            Response::returnData(400,'Bad Request');
        }else{
            $this -> nonceStr = $nonceStr;
            $this -> timestamp = $timestamp;
            $this -> signature = $signature;
            $this -> checkSign();
        }
    }

}