<?php

namespace libs;

class Request
{
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
}