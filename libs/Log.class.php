<?php

namespace libs;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log
{
    private static $instance;
    private $path = LOG_PATH;
    /**
     * @content 构造函数
     */
    private function __construct()
    {
        
    }
    /**
     * @content 防止克隆
     */
    private function __clone()
    {}
    /**
     * @content 公共方法
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    /**
     * @content 将日志写入文件
     */
    public function writeLog($data,$level)
    {
        //判断是否存在，不存在则创建
        $path = $this->path . DIRECTORY_SEPARATOR . $level;
        $this -> checkDir($path);
        $file = $path . DIRECTORY_SEPARATOR . date('Y-m-d').'.log';
        $data = "[".date('Y-m-d H:i:s')."]-[{$level}]: $data\r";
        $res = file_put_contents($file,$data,FILE_APPEND);
        if ($res) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * @content 判断目录是否存在，如果不存在则创建
     */
    public function checkDir($path)
    {
        if (!file_exists($path)) {
            mkdir($path,0777,true);
        }
    }

    /**
     * @content 调用monolog日志系统
     */
    public function monolog($data,$name,$level='INFO')
    {
        //判断是否存在，不存在则创建
        $path = $this->path . DIRECTORY_SEPARATOR . $level;
        $this -> checkDir($path);
        $file = $path . DIRECTORY_SEPARATOR . date('Y-m-d').'.log';
        // 创建日志频道
        $log = new Logger($name);
        $log->pushHandler(new StreamHandler($file, Logger::DEBUG));
        // 添加日志记录
        switch ($level) {
            case 'WARNING':
                $log->addWarning($data);
                break;
            case 'ERROR':
                $log->addError($data);
                break;
            case 'INFO':
                $log->addInfo($data);
                break;
            
            default:
                $log->addInfo($data);
                break;
        }
    }
}