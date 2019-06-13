<?php

namespace libs;

class HttpClient
{
    /**
     * @content fopen实现get post的请求
     */
    public static function fopenHttp($url, $data = '')
    {
        $opts = [];
        if (is_array($data)) {
            //如果是数组，则为 post请求
            $data = http_build_query($data);
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-Length: ' . strlen($data) . "\r\n",
                    'content' => $data,
                ],
            ];
            // dd($opts);
        }
        //创建资源流上下文
        $context = stream_context_create($opts);
        //打开一个资源或地址并绑定到一个流上
        $resource = fopen($url, 'r', false, $context);
        //对一个已经打开的资源流进行操作，并将其内容写入一个字符串返回
        $str = stream_get_contents($resource);
        return $str;
    }
    /**
     * @content file实现http请求
     */
    public static function fileHttp($url, $data = '')
    {
        $opts = [];
        if (is_array($data)) {
            $data = http_build_query($data);
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-Length: ' . strlen($data) . "\r\n",
                    'content' => $data,
                ],
            ];
        }
        //创建资源流上下文
        $context = stream_context_create($opts);
        //打开一个资源地址绑定到一个流上
        $content = file($url, 0, $context);
        //获取值
        $content = implode("\r\n", $content);
        return $content;
    }
    /**
     * @content 使用file_get_contents实现http请求
     */
    public static function fileGetContentsHttp($url, $data = '')
    {
        if (is_array($data)) {
            //post请求
            $data = http_build_query($data);
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n" . 'Content-Length: ' . strlen($data) . "\r\n",
                    'content' => $data
                ],
            ];
            $context = stream_context_create($opts);
            $content = \file_get_contents($url, false, $context);
        } else {
            //get请求
            $content = file_get_contents($url);
        }
        return $content;
    }
    /**
     * @content 使用fsockopen实现http请求
     */
    public static function fsockopenHttp($url, $data = '', $port = 80)
    {
        $parameter = \parse_url($url);
        // dump($parameter);
        if (isset($parameter['query'])) {
            $query = '?' . $parameter['query'];
        } else {
            $query = '/';
        }
        $fp = fsockopen($parameter['host'], $port, $errno, $errstr, 30);
        //打开链接
        if (is_array($data)) {
            $data = http_build_query($data);
            //编写http报文 post
            $httpStr = 'POST ' . $query . ' HTTP/1.1' . "\r\n";
            $httpStr .= 'Host: ' . $parameter['host'] . "\r\n";
            $httpStr .= 'Content-type: application/x-www-form-urlencoded' . "\r\n";
            $httpStr .= 'Content-Length: ' . strlen($data) . "\r\n";
            $httpStr .= "\r\n";
            $httpStr .= $data;
        } else {
            //编写http报文 get
            $httpStr = 'GET ' . $query . ' HTTP/1.1' . "\r\n";
            $httpStr .= 'Host: ' . $parameter['host'] . "\r\n";
            $httpStr .= 'Accept: */*' . "\r\n";
            $httpStr .= "\r\n";
        }
        // dd($httpStr);
        // 发送请求
        fwrite($fp, $httpStr);
        //接受响应
        $content = stream_get_contents($fp);

        return self::parseHttpResponsePackage($content);
    }
    /**
     * @content 
     */
    public static function streamHttp($url, $data = "", $port = "80")
    {
        $method = 'GET';
        $parameter = parse_url($url);
        $path = $parameter['path'] ?? '/';
        if (isset($parameter['query'])) {
            $path .= "?" . $parameter['query'];
        }
        // 打开连接
        $socket = stream_socket_client("tcp://" . $parameter['host'] . ":" . $port, $errno, $errstr, 30);
        if (!$socket) {
            // 抛出错误信息
        }
        // 编写HTTP报文
        $httpStr = "GET " . $path . " HTTP/1.1\r\n";
        if (is_array($data) && !empty($data)) {
            $method = "POST";
            $httpStr = "POST " . $parameter['path'] . '?' . $parameter['query'] . " HTTP/1.1\r\n";
            // 进行post请求
            $data = http_build_query($data);
            $httpStr .= "Content-Length: " . strlen($data) . "\r\n";
            $httpStr .= "Content-Type: application/x-www-form-urlencoded\r\n";
        }

        $httpStr .= "Host: " . $parameter['host'] . "\r\n";
        $httpStr .= "Accept: */*\r\n";
        $httpStr .= "\r\n";

        if ($method == "POST") {
            $httpStr .= $data;
        }
        // 发送HTTP报文
        fwrite($socket, $httpStr);

        // 接收响应
        $contents = stream_get_contents($socket);
        return self::parseHttpResponsePackage($contents);
    }
    /**
     * @content 解析报文
     */
    protected static function parseHttpResponsePackage($content)
    {
        // 按照空行进行分隔得到报文头以及报文实体
        list($http_header, $http_body) = explode("\r\n\r\n", $content);
        // 得到起始行
        $http_header = explode("\r\n", $http_header);

        list($schema, $code, $codeInfo) = explode(" ", $http_header[0]);
        unset($http_header[0]);

        $headers = [];
        foreach ($http_header as $v) {
            list($key, $value) = explode(": ", $v);
            $headers[$key] = $value;
        }
        //var_dump($headers);
        // 得到内容
        $body = "";
        if (isset($headers['Transfer-Encoding'])) {
            while ($http_body) {
                // 进行分割
                $httpBody = explode("\r\n", $http_body, 2);
                $chunkedSize = intval($httpBody[0], 16);
                $body .= substr($httpBody[1], 0, $chunkedSize);
                $http_body = substr($httpBody[1], $chunkedSize + 2);
            }
        } else {

            $body = $http_body;
        }
        // 返回响应头和内容数组
        return ["status" => [$schema, $code, $codeInfo], "header" => $headers, "body" => $body];
    }
}
