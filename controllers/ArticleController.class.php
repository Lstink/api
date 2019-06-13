<?php

namespace controllers;

use controllers\UserCommonController;
use models\ArticleModel;
use libs\Response;

class ArticleController extends UserCommonController
{
    /**
     * @content 文章列表的查询
     */
    public function actionArticleList()
    {
        // dd($this -> user);
        //查询用户的列表
        $model = new ArticleModel;
        $res = $model -> getArticleInfo();
        if (!$res) {
            Response::returnData(1006,'Article list is empty');
        }
        Response::returnData(200,'ok',$res);
    }
    /**
     * @content 查询文章的内容
     */
    public function actionArticleDetail()
    {
        $id = request() -> only('id');
        $model = new ArticleModel;
        $res = $model -> getArticleInfoFromId($id);
        if (!$res) {
            Response::returnData(1007,'Article is empty');
        }
        Response::returnData(200,'ok',$res);
    }
}