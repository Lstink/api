<?php

namespace libs;

class Config
{
    private $configs;
    private static $instance;
    /**
     * @content 构造函数
     */
    private function __construct($path='')
    {
        $config = [CONFIG_PATH,$path];
        $configs = [];
        // dd($this -> getFile(CONFIG_PATH));
        foreach ($config as $value) {
            //根据路径获取里面的文件
            $files = $this -> getFile($value);
            foreach ($files as $v) {
                //获取文件内的内容
                $configs = array_merge($this -> parseFile($v),$configs);

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
            if (($fp = opendir($path)) != false) {
                while (($file = readdir($fp)) != false) {
                    $dir = $path.DIRECTORY_SEPARATOR.$file;
                    if ($file != '.' && $file != '..') {
                        if (is_dir($dir)) {
                            $arr = $this -> getFile($dir);
                            foreach ($arr as $v) {
                                $files[] = $v;
                            }
                        }else{
                            $files[] = $dir;
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
    public function parseFile($path)
    {
        //解析一个配置文件，将其转化为数组
        return parse_ini_file($path, true);
    }
    /**
     * @content 设置单个配置项
     */
    public function setConfig($key,$value)
    {
        $arr = explode('.',$key);
        $num = count($arr);
        // 1说明为 name 2且最后一个为''则为name. 
        if ($num == 1 || $arr[1] == '') {
            $this -> configs[$arr[0]] = $value;
        }else if($num == 2){
            $this -> configs[$arr[0]][$arr[1]] = $value;
        }
    }
    /**
     * @content 获取单个文件内的内容
     */
    public function getConfig($name='')
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
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}