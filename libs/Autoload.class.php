<?php

// namespace libs;

class Autoload
{
    /**
     * @content 构造函数
     */
    public function __construct()
    {
        //注册自动加载方法
        spl_autoload_register([$this,'_autoload']);
        //加载函数库
        $this -> loadFun(FUN_PATH);
    }
    /**
     * @content 自动加载文件
     */
    protected function _autoload($className)
    {
        $ext = '.class.php';
        //传过来的值 classname 为 controller\classname
        $file = str_replace('\\',DIRECTORY_SEPARATOR,$className).$ext;
        $file = APP_PATH.DIRECTORY_SEPARATOR.'../'.$file;
        
        if (file_exists($file)) {
            include_once $file;
        }
    }
    /**
     * @content 加载函数库
     */
    protected function loadFun($path)
    {
        if (is_dir($path)) {
            if ($dir = opendir($path)) {
                while ($file = readdir($dir)) {
                    if ($file != '.' && $file != '..') {
                        include_once $path.DIRECTORY_SEPARATOR.$file;
                    }
                }
            }
        }
    }
}

new Autoload();