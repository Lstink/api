<?php

namespace controllers;

class EncryptController
{
    /**
     * @content 生成公钥私钥对
     */
    public function opensslRsa()
    {
        //配置文件
        $config = [
            'config'=>'K:\phpStudy\PHPTutorial\Apache\conf\openssl.cnf',
            'private_key_bits' => '2048'
        ];
        //创建资源
        $res = openssl_pkey_new($config);
        //生成私钥
        openssl_pkey_export($res, $privKey,null,$config);
        dump($privKey);
        //获取公钥的详细信息
        $details = openssl_pkey_get_details($res);
        $publicKey = $details['key'];
        dump($publicKey);
        //私钥加密
        openssl_private_encrypt('您好',$encrypt,$privKey);
        $encrypt = base64_encode($encrypt);
        dump($encrypt);
        //私钥解密
        openssl_public_decrypt(base64_decode($encrypt),$decrypt,$publicKey);
        dump($decrypt);
    }

}