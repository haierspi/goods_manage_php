<?php
namespace ff\auth;

use ff;
use ff\base\Application;
use ff\base\Controller;
use ff\code\ErrorCode;
use ff\helpers\StringLib;
use ff\network\Request;

class AliMessagePushSignAuthController extends Controller
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

        $appSecret = ff::$config['1688']['purchaseApp']['appSecret'];
        $message = $this->request->vars['message'];
        $_aop_signature = $this->request->vars['_aop_signature'];

        $sign_str = 'message' . $message;
        $code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));

        if ($code_sign != $_aop_signature) {
            return ErrorCode::SIGN_FAILED();
        }

        return null;
    }
}
