<?php

namespace libs;

class HTTP
{
    /**
     * @content curl的get方法
     */
    public function getHttp($url)
    {
        $options = [CURLOPT_POST => false];
        if ($this -> isHttps($url)) {
            $options = $this -> https($options);
        }
        return $this -> doHttp($url,$options);
    }
    /**
     * @content curl的post方法
     */
    public function postHttp($url,$data)
    {
        $options = [CURLOPT_POST => true,CURLOPT_POSTFIELDS => $data];
        if ($this -> isHttps($url)) {
            $options = $this -> https($options);
        }
        return $this -> doHttp($url,$options);
    }
    /**
     * @content 处理请求
     */
    private function doHttp($url,$options=[])
    {
        $ch = curl_init();
        $option = [
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
        ];
        $data = $option + $options;
        curl_setopt_array($ch,$data);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    /**
     * @content https的方法
     */
    private function https($options)
    {
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_SSL_VERIFYPEER] = false;

        return $options;
    }
    /**
     * @content 判断是否需要https协议
     */
    private function isHttps($url)
    {
        $res = strpos($url,'https://');
        if ($res == 0) {
            return true;
        }else{
            return false;
        }
    }
}