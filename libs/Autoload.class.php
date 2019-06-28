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

        //注册异常处理机制
        if (config('config.errorHandler')) {
            set_exception_handler([$this,'exceptionHandler']);
            set_error_handler([$this,'errorHandler']);
            register_shutdown_function([$this,'shutdownHandler']);
        }
    }
    /**
     * @content 自动加载文件
     */
    protected function _autoload($className)
    {
        $ext = '.class.php';
        //自动获取的值 classname 为 controller\classname
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
        //判断路径是否是文件夹
        if (is_dir($path)) {
            //如果是文件夹则打开文件夹，获得目录句柄
            if ($dir = opendir($path)) {
                //从目录句柄中读取条目
                while ($file = readdir($dir)) {
                    if ($file != '.' && $file != '..') {
                        include_once $path.DIRECTORY_SEPARATOR.$file;
                    }
                }
            }
        }
    }
    /**
     * @content 注册异常处理机制 try catchk 可以捕获的错误
     */
    public function exceptionHandler(Throwable $e)
    {
        $message = $e -> getMessage() . ' in ' . $e -> getFile() . ' on line ' . $e -> getLine();
        //记录日志
        writeLog($message);
        //发送邮件
        if (config('config.errorSendEmail')) {
            sendEmail($message);
        }
        // die(1);
        libs\Response::restfulResponse(500,['error'=>"内部错误"]);

    }
    /**
     * @content try catch 不能捕获的错误
     */
    public function errorHandler($errno,$errstr,$errFile,$errLine)
    {
        //将错误抛出，抛出后，此时能被捕获
        throw new ErrorException($errstr,0,$errno,$errFile,$errLine);
    }
    /**
     * @content error_shutdown_function
     */
    public function shutdownHandler()
    {
        if (!is_null($error = error_get_last()) && $this -> isFatal($error['type'])) {
            //抛出错误
            $this->exceptionHandler(new ErrorException( $error['message'], $error['type'], 0, $error['file'], $error['line']));
        }
    }
    /**
     * @content 判断错误类型是否属于列出这几种
     */
    public function isFatal($type)
    {
        // 以下错误无法被 set_error_handler 捕获: E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
        return is_array($type,[E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }

}

new Autoload();