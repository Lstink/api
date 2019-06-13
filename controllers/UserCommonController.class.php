<?php

namespace controllers;

use libs\Controller;
use libs\Response;
use models\UserModel;

//所有需要表单令牌的类都需要继承此类
class UserCommonController extends Controller
{
    protected $user;

    public function __construct()
    {
        //执行父类的构造方法
        parent::__construct();
        //验证令牌
        $this -> verifyToken();
    }
    /**
     * @content 验证令牌
     */
    public function verifyToken()
    {
        //获取token
        $token = request() -> only('access_token')??null;
        if (!$token) {
            Response::returnData(1004,'access_token not exists');
        }
        $model = new UserModel;
        //根据表单令牌验证token
        $res = $model -> verifyToken($token);
        if (!$res) {
            Response::returnData(1005,'The user does not exist');
        }else if(is_string($res)){
            Response::returnData(401,$res);
        }
        $this->user = $res;
    }
}