<?php
namespace ff\auth;

use ff;
use ff\base\Application;
use ff\base\Controller;
use ff\helpers\StringLib;
use ff\network\Request;
use ff\code\ErrorCode;

class SignAuthController extends Controller
{
    public function beforeAction()
    {
        $verifyResult = $this->auth();
        if (!is_null($verifyResult)) {
            return $verifyResult;
        }
        parent::beforeAction();
    }

    public function auth($callrunController = null, $vars = null)
    {
        if (is_null($vars)) {
            $vars = $this->request->vars;
        }
        $signkey = ff::$config['signkey'];

        if (!isset($vars['sign']) || $vars['sign'] != StringLib::getArySign($vars, $signkey)) {
            return ErrorCode::SIGN_FAILED();
        }
        if (!is_null($callrunController)) {
            $callrunController->sign = $this->sign;
        }

        return null;
    }
}
