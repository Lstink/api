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
        $config = \libs\Config::getInstance();
        $config -> setConfig('database.username','yyy');
        $res = config('database');
        dd($res);

    }
    public function actionUploads()
    {
        $data = '----ASD'."\r\n";
        $data .= 'Content-Disposition: form-data; name="username"'."\r\n\r\n";
        $data .= '姬发式十分健康'."\r\n";
        $data = '----ASD'."\r\n";
        $data .= 'Content-Disposition: form-data; name="username"; filename="ddsds.jpg"'."\r\n\r\n";
        $data .= file_get_contents('./ddsds.jpg')."\r\n";
        $data .= '----ASD--'."\r\n\r\n";

        //拼装报文
        $form = "POST /index.php?c=upload&a=upload HTTP/1.1\r\n";
        $form .= 'Host: www.api.com'."\r\n";
        $form .= 'Content-type: multipart/form-data; boundary=--ASD'."\r\n";
        $form .= 'Content-Length: '.strlen($data)."\r\n\r\n";
        $form .= $data;

        $fp = fsockopen('www.api.com','80',$errno,$errStr,30);
        fwrite($fp,$form);
        $res = stream_get_contents($fp);
        dd($res);
    }
}