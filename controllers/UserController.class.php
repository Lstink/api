<?php

namespace controllers;

use libs\Controller;
use libs\Response;
use models\UserModel;

class UserController extends Controller
{
    /**
     * @content 实现用户登陆的方法
     */
    public function actionLogin()
    {
        //获取用户输入的帐号密码
        $username = request() -> only('username');
        $password = request() -> only('password');
        // dd($password);
        //查询是否存在该账号
        $model = new UserModel;
        $res = $model -> getUserInfoByUserName($username);
        //如果不存在
        if (empty($res)) {
            Response::returnData(1002,'The username does not exist');
        }
        //验证密码不正确时
        if(!password_verify($password,$res['pwd'])){
            Response::returnData(1003,'password error');
        }
        //通过验证 生成token
        $user_token = $model -> createToken($res['id']);
        // echo $user_token;
        // die;
        
		Response::returnData(200,"ok",['access_token'=>$user_token]);
    }
    /**
     * @content 生成token
     */
    public function createToken($len=30)
    {
        //生成一个唯一的微秒级别的id
        $rand = uniqid();
        $str = $rand.\createRandString($len-strlen($rand));
        return $str;
    }

}