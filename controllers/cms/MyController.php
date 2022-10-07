<?php
namespace controllers\cms;

use common\Admin;
use ff;
use ff\auth\CookieAuthController;
use ff\base\Controller;
use models\tables\AdminUserModel;

class MyController extends CookieAuthController
{

    public function actionIndex()
    {
        Admin::globalViewAssign();
        return viewfile('cms/home/index');

    }


    public function actionPassword()
    {
        if($_POST){

            $oldpassword = $this->request->vars['oldpassword'];
            $password = $this->request->vars['password'];
            $passwordrepeat = $this->request->vars['passwordrepeat'];

            if( empty($oldpassword) || empty($password) || empty($passwordrepeat)){
                return Admin::message('请输入密码','my/password');
            }
    
            if($password != $passwordrepeat){
                return Admin::message('新密码不一致','my/password');
            }

            $adminUserModel = new AdminUserModel();
            $adminUser = $adminUserModel->getUserByField($this->user->auid, 'auid');

     

            if (!$adminUser) {
                return Admin::message('用户不存在或被禁用！');
            }
    
            if (!$adminUserModel->checkPassword($oldpassword, $adminUser->password)) {
                return Admin::message('用户原密码不正确');
            }
    
    
            $adminUser->password = $adminUser->getCalcPw($password);
            $adminUser->save();

            return Admin::message('密码修改成功','my/password');


        }else{

            Admin::globalViewAssign();
            viewAssign('baseurl', constant('RUNTIME_HTTP_HOST'));
            return viewfile('cms/my/password');
    
        }
    }


    public function actionLogout()
    {
        Admin::logout();
        return Admin::message('成功注销','user/login');

    }


}
