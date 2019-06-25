<?php

namespace controllers;

use libs\{ HttpClient , FileUpload };
use controllers\EncryptController;


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
        // $config = \libs\Config::getInstance();
        // $config -> setConfig('database.username','yyy');
        $res = config('qiniu.AccessKey');
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
    /**
     * @content 上传到七牛云
     */
    public function actionUploadByQiNiu()
    {
        $data = [
            'name' => '1231.jpg',
            'content' => base64_encode(\file_get_contents('./1552965135434.jpg')),
        ];
        $res = HttpClient::fopenHttp('http://www.apitest.com/index.php?c=upload&a=uploadByQiniu',$data);
        echo($res);
    }
    /**
     * @content 跨域测试
     */
    public function actionAjaxUpload()
    {
        include_once '../views/ajaxUpload.html';
    }
    /**
     * @content 多文件ajax上传
     */
    public function actionAjaxUploads()
    {
        include_once '../views/ajaxUploads.html';
    }
    /**
     * @content 文件头的处理
     */
    public function actionHeader()
    {
        $header = request() -> header();
        dd($header);
    }
    /**
     * @加密解密测试
     */
    public function actionCrypt()
    {
        $data = encrypt('555');
        dump($data);
        $data = decrypt($data);
        dump($data);
        $num = \encryptCBC('f5df5fd5f');
        dump($num);
        $num = decryptCBC($num);
        dump($num);
    }
    /**
     * @content 加密解密的控制器
     */
    public function actionDemoCrypt()
    {
        $p = new EncryptController;
        $p->opensslRsa();
    }
}