<?php

namespace ff\auth;


use common\SussedCode;
use ff;
use ff\base\Controller;
use Firebase\JWT\JWT;

/**
 * JWT验证控制器基类
 */
class JWTAuthController extends Controller
{
    public function beforeAction()
    {
        $ret = $this->auth();
        if (!is_null($ret)) {
            return $ret;
        }
        parent::beforeAction();
    }

    /**
     * JWT权限验证
     * @author 陆树文
     * @return null
     */
    protected function auth()
    {
        $token = $this->request->vars['jwt_token'];
        if (empty($token)) {
            return SussedCode::FAILED('参数jwt_token必传');
        }
        $jwt_config = ff::$config['jwt'];
        try {
            // 这里解析会得到登录时存的data内容
            JWT::decode($token, $jwt_config['key'], ['HS256']);
        } catch (\Throwable $e) {
            return SussedCode::FAILED('无效的jwt_token');
        }
        return null;
    }
}