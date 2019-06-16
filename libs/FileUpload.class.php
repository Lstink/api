<?php

namespace libs;

class FileUpload
{
    private $config = [
        'ext' => ['jpg','jpeg','png','gif'],
        'mimeType' => ['image/jpeg','image/png','image/gif'],
        'savePath' => './uploads',
        'size' => 1024*5*1024,
        'transferByBase64' => false,
    ];
    private $errorNo = 0;
    private $type;
    private $size;
    private $tmpName;
    private $savePath;
    private $ext;
    private $name;
    private $newName;

    /**
     * @content 构造函数
     */
    public function __construct($config = [])
    {
        $this -> config = array_merge($this -> config,$config);
    }
    /**
     * @content 文件上传
     */
    public function uploads()
    {
        $file = $_FILES;

        foreach ($file as $v) {
            $file = $v;
        }
        $num = 1;
        if (is_array($file['name'])) {
            $num = count($file['name']);
        }
        if ($num == 1) {
            //单文件上传 调用文件上传核心方法
            $res = $this -> upload($file);
            return $res;
        }else if($num > 1){
            //多文件上传
            return $this -> uploadMany($file);
        }
        
    }
    /**
     * @content 批量上传
     */
    private function uploadMany($files)
    {
        $arr = [];
        for ($i=0; $i < count($files['name']); $i++) { 
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];
            $res = $this -> upload($file);
            if (!$res) {
                continue;
            }
            array_push($arr,$res);
        }
        return $arr;
    }
    /**
     * @content 核心上传文件方法
     */
    private function upload($file)
    {
        //验证文件上传是否出错
        if (!$this -> checkError($file)) {
            return false;
        }
        //验证文件类型是否正确
        if (!$this -> checkFileType()) {
            return false;
        }
        //验证文件的大小是否符合规范
        if (!$this -> checkFileSize()) {
            return false;
        }
        //判断上传文件的目录是否存在，不存在则创建
        if (!$this -> checkDir()) {
            return false;
        }
        //将文件从临时文件夹移动到指定文件夹
        if (!$this -> moveFile()) {
            return false;
        }
        //上传成功 返回数据
        $result = [
            'name' => $this -> name,
            'newName' => $this -> newName,
            'path' => $this -> savePath,
            'size' => $this -> size,
        ];
        return $result;

    }
    /**
     * @content 通过流的形式上传
     */
    public function uploadByStream($name,$size,$str)
    {
        //判断文件后缀名
        if (!in_array(pathinfo($name,PATHINFO_EXTENSION),$this -> config['ext'])) {
            $this -> errorNo = 8;
            return false;
        }
        //验证文件的大小是否符合规范
        if (!$this -> checkFileSize($size)) {
            return false;
        }

        //判断上传文件的目录是否存在，不存在则创建
        if (!$this -> checkDir()) {
            return false;
        }
        $this -> ext = pathinfo($name,PATHINFO_EXTENSION);
        $this -> newName = $this -> getNewFileName();
        $res = file_put_contents($this -> savePath.DIRECTORY_SEPARATOR.$this -> newName,base64_decode($str));
        if ($res) {
            //上传成功 返回数据
            $result = [
                'name' => $this -> name,
                'newName' => $this -> newName,
                'path' => $this -> savePath,
                'size' => $this -> size,
            ];
            return $result;
        }
    }
    /**
     * @content 为单个成员设置值
     */
    public function setOption($key,$val)
    {
        $this -> config[$key] = $val;
    }
    /**
     * @content 获取单个属性值
     */
    public function getOption($name)
    {
        return $this -> config[$name];
    }
    /**
     * @content 获取上传文件后的文件名
     */
    public function getFileName()
    {
        return $this -> newName;
    }
    /**
     * @content 错误信息定义
     */
    public function getError()
    {
        $errorNo = $this -> errorNo;
        switch ($errorNo) {
            case UPLOAD_ERR_INI_SIZE:
                return '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                return '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
                break;
            case UPLOAD_ERR_PARTIAL:
                return '文件只有部分被上传';
                break;
            case UPLOAD_ERR_NO_FILE:
                return '没有文件被上传';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                return '找不到临时文件夹';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                return '文件写入失败';
                break;
            case '8':
                return '文件后缀名不符合规范';
                break;
            case '9':
                return '文件类型不符合规范';
                break;
            case '10':
                return '文件类型被篡改';
                break;
            case '11':
                return '文件大小不能超过' . ($this->config['size'] / 1024 / 1024) . 'MB';
                break;
            case '12':
                return '非法上传文件';
                break;
            case '13':
                return '文件移动失败';
                break;
            
        }
    }
    /**
     * @content 检查文件上传是否出错
     */
    private function checkError($file)
    {
        if ($file['error'] != 0) {
            $this -> errorNo = $file['error'];
            return false;
        }
        $this -> name = $file['name'];
        $this -> type = $file['type'];
        $this -> size = $file['size'];
        $this -> tmpName = $file['tmp_name'];
        $this -> ext = pathinfo($file['name'],PATHINFO_EXTENSION);
        return true;
    }
    /**
     * @content 检查文件类型是否正确
     */
    private function checkFileType()
    {
        //判断文件后缀名
        if (!in_array($this -> ext,$this -> config['ext'])) {
            $this -> errorNo = 8;
            return false;
        }
        //判断文件类型
        if (!in_array($this -> type,$this -> config['mimeType'])) {
            $this -> errorNo = 9;
            return false;
        }
        //判断文件是否被篡改
        if (mime_content_type($this -> tmpName) != $this -> type) {
            $this -> errorNo = 10;
            return false;
        }
        return true;
    }
    /**
     * @content 验证上传文件的大小
     */
    private function checkFileSize()
    {
        if ($this -> size > $this -> config['size']) {
            $this -> errorNo = 11;
            return false;
        }
        return true;
    }
    /**
     * @content 判断上传文件的目录是否存在不存在则创建
     */
    private function checkDir()
    {
        $timePath = date('Ymd');
        $path = $this -> config['savePath'].DIRECTORY_SEPARATOR.$timePath;
        if (!is_dir($path)) {
            //创建文件夹
            mkdir($path,0777,true);
        }
        $this -> savePath = $path;
        return true;
    }
    /**
     * @content 将文件从临时文件夹移动到指定文件中
     */
    private function moveFile()
    {
        if (!is_uploaded_file($this -> tmpName)) {
            $this -> errorNo = 12;
            return false;
        }
        $this -> newName = $this -> getNewFileName();
        if (!move_uploaded_file($this -> tmpName,$this -> savePath.DIRECTORY_SEPARATOR.$this -> newName)) {
            $this -> errorNo = 13;
            return false;
        }
        return true;
        
    }
    /**
     * @content 生成唯一的文件名称
     */
    private function getNewFileName()
    {
        return uniqid().createRandString(5).'.'.$this -> ext;
    }
}