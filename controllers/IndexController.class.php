<?php

namespace controllers;

use models\UserModel;
use libs\Controller;
use controllers\UserCommonController;

class IndexController extends UserCommonController
{
    public function actionIndex()
    {
        dd($this -> user);
    }
}