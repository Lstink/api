<?php

namespace libs;

class Config
{
    private $configs;
    private static $instance;
    /**
     * @content 构造函数
     */
    public function __construct($path='')
    {
        $config = [CONFIG_PATH,$path];
        $configs = [];
        foreach ($config as $value) {
            //根据路径获取里面的文件
            $files = $this -> getFile($value);
            foreach ($files as $k => $v) {
                //获取文件内的内容
                $configs = array_merge($this -> parseFile($k, $v,$value),$configs);

            }
        }
        $this -> configs =  $configs;
    }
    /**
     * @content 获取路径内的文件
     */
    public function getFile($path)
    {
        $files = [];
        if (is_dir($path)) {
            if ($fp = opendir($path)) {
                while ($file = readdir($fp)) {
                    if ($file != '.' && $file != '..') {
                        if (is_dir($path.DIRECTORY_SEPARATOR.$file)) {
                            $files[$file] = $this -> getFile($path.DIRECTORY_SEPARATOR.$file);
                        }else{
                            $files[] = $file;
                        }
                    }
                }
            }
        }
        return $files;
    }
    /**
     * @content 获取文件内的内容
     */
    public function parseFile($key,$file,$path)
    {
        $arr = [];
        if (is_array($file)) {
            foreach ($file as $k => $v) {
                $arr = array_merge($arr,parse_ini_file($path.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$v, true));
            }
        }else{
            $arr = array_merge($arr,parse_ini_file($path.DIRECTORY_SEPARATOR.$file, true));
        }
        return $arr;
    }
    /**
     * @content 获取单个文件内的内容
     */
    public function getConfig($name)
    {
        $arr = explode('.',$name);
        $num = count($arr);
        // 1说明为 name 2且最后一个为''则为name. 
        if ($num == 1 || $arr[1] =='') {
            return $this -> configs[$arr[0]]??null;
        }else if($num == 2){
            return $this -> configs[$arr[0]][$arr[1]]??null;
        }
    }
    private function __clone()
    {}
    private static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}