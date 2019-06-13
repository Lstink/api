<?php

namespace models;

use libs\Model;

class ArticleModel extends Model
{
    /**
     * @content 查询所有文章列表
     */
    public function getArticleInfo()
    {
        return $this -> query('select * from __table__');
    }
    /**
     * @content 根据id查询文章具体内容
     */
    public function getArticleInfoFromId($id)
    {
        $data = $this -> query('select content from __table__ where id=?',[$id]);
        return $data['content'];
    }
}