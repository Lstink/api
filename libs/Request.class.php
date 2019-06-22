<?php

namespace libs;

class Request
{
    protected $headers;
    public function __construct()
    {
        $this -> headers = $this -> getHeaders();
    }
    /**
	 * @content get请求
	 */
	public function get($name = '')
	{
		if (empty($name)) {
			return $_GET;
        }
        return $_GET[$name]??null;
	}
	/**
	 * @content post请求
	 */
	public function post($name = '')
	{
		if (empty($name)) {
			return $_POST;
        }
        return $_POST[$name]??null;
	}
	/**
	 * @content all
	 */
	public function all()
	{
        return $_REQUEST;
    }
    /**
     * @content 排除变量
     */
    public function except($name)
    {
        $data = $_REQUEST;
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                unset($data[$v]);
            }
        }else{
            unset($data[$name]);
        }
        return $data;
    }
    /**
     * @content 获取单一变量
     */
    public function only($name)
    {
        $data = [];
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                if (isset($_REQUEST[$v])) {
                    $data[$v] = $_REQUEST[$v];
                }
            }
        }else{
            if (isset($_REQUEST[$name])) {
                $data = $_REQUEST[$name];
            }
        }
        return $data;
    }
    /**
     * @content 获取http header头
     */
    public function getHeaders()
    {
        $headers = [];
        $header = $_SERVER;
        //如果有content-type或者content-length
        isset($_SERVER['CONTENT_TYPE']) && $headers['Content-Type']= $_SERVER['CONTENT_TYPE'];
        isset($_SERVER['CONTENT_LENGTH']) && $headers['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        
        foreach ($header as $key => $val) {
            if (strpos($key,'HTTP_') === 0) {
                $key = $this -> turnKey($key);
                $headers[$key] = $val;
            }
        }
        // $header = strpos();
        return $headers;
    }
    /**
     * @content 处理key
     */
    private function turnKey($key)
    {
        $key = str_replace('HTTP_','',$key);
        $key = explode('_',$key);
        $key = array_map(function($v){
            return ucfirst(strtolower($v));
        },$key);
        $key = implode('-',$key);
        return $key;
    }
    /**
     * @content header方法
     */
    public function header($name='')
    {
        return empty($name)?$this -> headers:($this -> headers[$name]??null);
    }
}