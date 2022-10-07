<?php
namespace ff\auth;

use common\Admin;
use ff\base\Controller;
use ff\code\ErrorCode;
use ff\database\AdminUserAuthModel;
use ff\helpers\Cookie;
use ff\helpers\TokenParse;

class CookieAuthController extends Controller
{
    protected $user;

    public function beforeAction()
    {
        $header = getallheaders();

        $verifyResult = $this->auth();
        if (!is_null($verifyResult)) {
            return $verifyResult;
        }

        parent::beforeAction();
    }

    /**
     * @name token校验
     * @param null $callrunController
     * @param null $vars
     * @return null
     */
    private function auth($callrunController = null, $vars = null)
    {

        $token = Cookie::getCookie(Admin::COOKIE_NAME, Admin::COOKIE_PRE);

        $token = $token ?? null;

        list($auid, $username, $expiration) = TokenParse::get($token, Admin::ENCRYPT_KEY);

        \ff::$app->router->request->auid = $auid;
        \ff::$app->router->request->username = $username;

        if (empty($auid)) {
            return Admin::message(ErrorCode::TOKEN_FAILED_MSG,'user/login');
        }

        if (!$expiration || $expiration < TIMESTAMP) {
            return Admin::message(ErrorCode::TOKEN_EXPIRED_MSG,'user/login');
        }

        return $this->init($auid, $token);
    }

    public function init($auid, $token)
    {
        //select db check user token
        $this->user = new AdminUserAuthModel();
        $this->user->init($auid, $token);

        if (is_null($this->user) || !$this->user->auid) {
            return Admin::message(ErrorCode::TOKEN_FAILED_MSG,'user/login');
        }

        return null;
    }

    public function checkAccess($x, $y, $z)
    {
        return "$x,$y,$z";
    }
}
