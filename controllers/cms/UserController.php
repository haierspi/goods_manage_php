<?php
namespace controllers\cms;

use common\Admin;
use DB;
use ff;
use ff\base\Controller;
use ff\database\Model;
use models\tables\AdminUserModel;

class UserController extends Controller
{

    public function actionIndex()
    {
        Admin::globalViewAssign();
        return viewfile('cms/user/login');
    }

    public function actionLogin()
    {

        if($_POST){
            $username = $this->request->vars['username'] ?? null;
            $password = $this->request->vars['password'];
    
            if (!$username || !$password) {
                return Admin::message('登录账号或者登录密码为空');
            }
    
            $username = trim($username);
    
            $adminUserModel = new AdminUserModel();
    
            $adminUser = $adminUserModel->getUserByField($username, 'username');
    
            if (!$adminUser) {
                return Admin::message('用户不存在或被禁用！');
            }
    
    
            if (!$adminUserModel->checkPassword($password, $adminUser->password)) {
                return Admin::message('用户密码不正确');
            }
    
    
            $token = $adminUser->getToken();
    
    
    
            //dd($_COOKIE);
    
            Admin::login($token);
    
            return Admin::message('登录成功正在跳转','home/index');
        }else{

            Admin::globalViewAssign();
            return viewfile('cms/user/login');

        }

    }


    
    public function actionTest()
    {
        $list =  DB::table("test")->get();

        $listjson = [];
        
        foreach ($list  as $item) {
            $listjson [$item->ItemID] = (array)$item;
        }

        exit;

    }

}
