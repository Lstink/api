<?php

namespace controllers;

use libs\{ HttpClient , FileUpload };

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
    public function actionUpload()
    {
        $upload = new FileUpload;
        // $upload -> setOption('transferByBase64',true);
        $res = $upload -> uploads();
        if (!$res) {
           echo $upload -> getError();
        }
    }
    public function actionConfig()
    {
        $res = config('ddd.ffff');
        dd($res);

    }
}