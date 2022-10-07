<?php
namespace controllers\cms;

use common\Admin;
use ff;
use ff\auth\CookieAuthController;
use ff\base\Controller;
use models\tables\AdminUserModel;

class HomeController extends CookieAuthController
{

    public function actionIndex()
    {

        
        Admin::globalViewAssign();
        return viewfile('cms/home/index');

    }


}
