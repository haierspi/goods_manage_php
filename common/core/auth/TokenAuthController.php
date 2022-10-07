<?php
namespace ff\auth;

use ff\base\Controller;
use ff\code\ErrorCode;
use ff\database\UserModel;
use ff\helpers\TokenParse;

class TokenAuthController extends Controller
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

        if (is_null($vars)) {
            if (isset($this->request->headerVars['Token'])) {
                $headerToken = $this->request->headerVars['Token'];
            } elseif (isset($this->request->headerVars['token'])) {
                $headerToken = $this->request->headerVars['token'];
            }
            $token = $this->request->vars['token'] ?? ($headerToken ?? null);
        }

        $token = $token ?? null;

        list($uid, $nickname, $expiration) = TokenParse::get($token);


        if (empty($uid)) {
            return ErrorCode::TOKEN_FAILED();
        }

        if (!$expiration || $expiration < TIMESTAMP) {
            return ErrorCode::TOKEN_EXPIRED();
        }

        return $this->init($uid, $token);
    }

    public function init($uid, $token)
    {
        //select db check user token
        $this->user = new UserModel();
        $this->user->init($uid, $token);

        if (is_null($this->user) || !$this->user->uid) {
            return ErrorCode::TOKEN_FAILED();
        }

        
        $this->uid = $this->user->uid;
        $this->nickname = $this->user->nickname;

        return null;
    }

    public function checkAccess($x, $y, $z)
    {
        return "$x,$y,$z";
    }
}
