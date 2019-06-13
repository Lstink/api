<?php

namespace controllers;

use libs\HttpClient;

class TestController
{
    public function actionTest()
    {
        $data = [
            'username' => 'zs',
            'password' => '123456',
            'id' => '2'
        ];
        $res = createSign($data);
        dd($res);
    }
    public function actionHttp()
    {
        $url = 'http://www.api.com/index.php';
        $data = [
            'username' => 'zs',
            'password' => '123456', 
        ];
        $res = HttpClient::fsockopenHttp($url);
    }
}