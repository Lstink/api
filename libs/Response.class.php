<?php

namespace libs;

/*
	用来做各种响应, [code,messag,data]
*/
class Response 
{

    public static function returnData($code,$message,$data=[])
    {
		$dt = '';

		$format = isset($_GET['format']) ? trim($_GET['format']) : "json";

		switch($format){

			case "xml" :
				$dt = self::xml($code,$message,$data);
				break;
			case "array":
				$dt = self::genData($code,$message,$data);
				break;

			default:
				$dt = self::json($code,$message,$data);
		}

		if(is_array($dt)){
			var_dump($dt);
			exit;
		}
		exit($dt);
	}

	// 生成通用的响应数组
    public static function genData($code,$message,$data=[])
    {
		return [

			"code" => $code,
			"message" => $message,
			"data" => $data
		];
	}

	// 响应json格式数据
    public static function json($code,$message,$data=[])
    {
		// 先得到通用数组
		$data = self::genData($code,$message,$data);
		return json_encode($data,JSON_UNESCAPED_UNICODE);
	} 

	// 响应xml格式的数据
    public static function xml($code,$message,$data=[])
    {
		$data = self::genData($code,$message,$data);

		// 生成xml格式
		$xml = "<? xml version='1.0' encoding='utf8' ?>";
		$xml .= "<root>";
		$xml .= "<code>".$data['code']."</code>";
		$xml .= "<message>".$data['message']."</message>";
		$xml .= "<data>";

		$xml .= self::genXml($data['data']);

		$xml .= "</data></root>";
		return $xml;

	}

	// 根据data生成xml数据
    private static function genXml($data)
    {
		$xml = "";
		foreach($data as $key=>$v){
			if(is_array($v)){
				$xml .= "<{$key}>";
				$xml .= self::genXml($v);
				$xml .= "</{$key}>";
			}else{
				$xml .= "<{$key}>{$v}</{$key}>";
			}
		}
		return $xml;
	}

}