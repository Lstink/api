<?php

namespace controllers;

use libs\{ FileUpload , Response };

class UploadController
{
    /**
     * @content 普通的 multipart/form-data 方式上传
     */
    public function actionUpload()
    {
        $uploads = new FileUpload();
        $res = $uploads -> uploads();
        if(!$res){
			Response::returnData(1008,'File upload failed',['error'=>$uploads->getError()]);
		}

		Response::returnData(200,"ok",$res);
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
        $res = $uploads -> uploadByStream($name,$size,$str);
        if(!$res){
			Response::returnData(1008,'File upload failed',['error'=>$uploads->getError()]);
		}

		Response::returnData(200,"ok",$res);
    }
    
    public function actionUploadByStream(){

		$file = file_get_contents("php://input");
		file_put_contents("./uploads/a.jpg", $file);
	}

	public function actionJson(){
		$data = file_get_contents("php://input");
		var_dump($data);
	}

	public function actionXml(){

		$data = file_get_contents("php://input");
		var_dump($data);
	}
}