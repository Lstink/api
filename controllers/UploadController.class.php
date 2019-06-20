<?php

namespace controllers;

use libs \ { FileUpload , Response };
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class UploadController
{
	/**
	 * @content 普通的 multipart/form-data 方式上传
	 */
	public function actionUpload()
	{
		header('Access-Control-Allow-Origin: *');
		$uploads = new FileUpload();
		$res = $uploads->uploads();
		if (!$res) {
			Response::returnData(1008, 'File upload failed', ['error' => $uploads->getError()]);
		}

		Response::returnData(200, "ok", $res);
	}
	/**
	 * @content base64的上传方式
	 */
	public function actionUploadByBase64()
	{

		$str = $_POST['str'];
		$name = $_POST['name'];
		$size = $_POST['size'];

		$uploads = new FileUpload();
		$res = $uploads->uploadByStream($name, $size, $str);
		if (!$res) {
			Response::returnData(1008, 'File upload failed', ['error' => $uploads->getError()]);
		}

		Response::returnData(200, "ok", $res);
	}

	public function actionUploadByStream()
	{

		$file = file_get_contents("php://input");
		file_put_contents("./uploads/a.jpg", $file);
	}

	public function actionJson()
	{
		$data = file_get_contents("php://input");
		var_dump($data);
	}

	public function actionXml()
	{

		$data = file_get_contents("php://input");
		var_dump($data);
	}
	/**
	 * @上传到七牛云
	 */
	public function actionUploadByQiNiu()
	{
		//生成自定义文件名称
		$file_name = request() -> post('name');
		// $file_name = $this -> getNewFileName($file_name);
		//获得文件的二进制流
		// $content = file_get_contents('./1552965135434.jpg');
		$content = base64_decode(request() -> post('content'));
		// dd($request() -> post('content'));
		$accessKey = config('qiniu.AccessKey');
		$secretKey = config('qiniu.SecretKey');
		$bucket = config('qiniu.buckName');
		// dd(1);
		$auth = new Auth($accessKey, $secretKey);
		// 生成上传Token
		$token = $auth->uploadToken($bucket);
		// 构建 UploadManager 对象
		$uploadMgr = new UploadManager();
		list($ret, $error) = $uploadMgr->putFile($token, $file_name, './1552965135434.jpg');
    	if($error){
    		Response::returnData(500,"INNER ERROR");
    	}
		$domain = config('qiniu.doMain');
    	Response::returnData(200,'OK',['path'=>$domain."/".$ret['key']]);
	}
	/**
     * @content 生成唯一的文件名称
     */
    private function getNewFileName($file_name)
    {
		$ext = explode('.',$file_name)[1];
        return uniqid().createRandString(5).'.'.$ext;
	}
	/**
	 * @content 大文件分片上传
	 */
	public function actionUploads()
	{
		header('Access-Control-Allow-Origin: *');
		$data = request() -> all();
		$data = $_FILES + $data;
		//生成md5文件夹名称
		$file_path = md5($data['file']['name']);
		$time_path = date('Y-m-d');
		//临时文件的完整路径
		$path = './uploads'.DIRECTORY_SEPARATOR.$time_path.DIRECTORY_SEPARATOR.$file_path;
		//判断是否存在这个文件夹 不存在则创建
		if (!file_exists($path)) {
			mkdir($path,0777,true);
		}
		//文件名称
		$file_name = $path.DIRECTORY_SEPARATOR.$data['num'].'.'.'tmp';
		//判断文件是否存在 不存在则上传
		if (!file_exists($file_name)) {
			//将文件写入
			file_put_contents($file_name,file_get_contents($data['file']['tmp_name']));
			//判断是否为最后一个文件
			if ($data['num'] == $data['sumNum']) {
				//合并文件，并删除临时文件夹
				$new_path = './uploads'.DIRECTORY_SEPARATOR.$time_path.DIRECTORY_SEPARATOR.$data['file']['name'];
				$res = $this -> mergeFile($path,$data['sumNum'],$new_path,$data['size']);
				if ($res) {
					Response::returnData(200,'OK',['path'=>$new_path]);
				}else{
					Response::returnData(500,'error');
				}
			}

		}
		
	}
	/**
	 * @content 遍历文件夹内的文件，并合并文件
	 */
	public function mergeFile($path,$num,$new_path,$size)
	{
		if (file_exists($new_path)) {
			//获取文件大小
			$fileSize = filesize($new_path);
			//上传完成
			if ($fileSize == $size) {
				//删除临时文件
				$res = $this -> delLink($path);
				if ($res) {
					return true;
				}else{
					return false;
				}
			}else{
				//删除文件重新合并
				unlink($new_path);
				return $this -> mergeFile($path,$num,$new_path,$size);
			}
		}else{
			//将文件合并
			for ($i=1; $i <= $num; $i++) { 
				file_put_contents($new_path,file_get_contents($path.DIRECTORY_SEPARATOR.$i.'.'.'tmp'),FILE_APPEND);
			}
			return $this -> mergeFile($path,$num,$new_path,$size);
		}
	}
	/**
	 * @content 删除文件及其目录
	 */
	public function delLink($path)
	{
		//删除临时文件夹及其文件
		if (is_dir($path)) {
			if ($fp = opendir($path)) {
				while($file = readdir($fp)){
					if ($file != '.' && $file != '..') {
						//$file为具体文件
						unlink($path.DIRECTORY_SEPARATOR.$file);
					}
				}
			}
		}
		rmdir($path);
		if (!file_exists($path)) {
			return true;
		}else{
			return false;
		}
	}
}
