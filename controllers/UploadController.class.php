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
}
