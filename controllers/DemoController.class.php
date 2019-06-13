<?php

namespace controllers;

use libs\HTTP;

class DemoController
{
    public function actionList()
    {
        //账号 zs 密码 123456
        //接收表单数据
        $data = request() -> post();
        $data = $this -> getData($data);
        //获取文章列表
        $res = $this -> getArticleList($data);
        echo $res;
    }

    public function actionDetail()
    {
        //接收表单数据
        $data = request() -> post();
        $data = $this -> getData($data);
        //获取文章列表
        $res = $this -> getArticleDetail($data);
        echo $res;
    }
    /**
     * @content 根据账号密码和其它数据验证签名 获得token
     */
    public function getData($data)
    {
        //创建签名
        $re = createSign($data);
        $data = $data + $re;
        //登陆获取access_token
        $res = $this -> login($data);
        $res = json_decode($res,true);
        //access_token
        $access_token = $res['data'];
        //拼装数组
        $data = $data + $access_token;
        return $data;
    }
    /**
     * @content 登陆方法 获取access_token
     */
    public function login($data)
    {
        $url = 'http://www.api.com/index.php?c=user&a=login';
        $http = new HTTP;
        $res = $http -> postHttp($url,$data);
        return $res;
    }
    /**
     * @content 获取文章列表
     */
    public function getArticleList($data)
    {
        $url = 'http://www.api.com/index.php?c=article&a=articleList';
        $http = new HTTP;
        $res = $http -> postHttp($url,$data);
        return $res;
    }
    /**
     * @content 获取文章内容
     */
    public function getArticleDetail($data)
    {
        $url = 'http://www.api.com/index.php?c=article&a=articleDetail';
        $http = new HTTP;
        $res = $http -> postHttp($url,$data);
        return $res;
    }
}