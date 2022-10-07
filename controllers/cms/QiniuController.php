<?php
namespace controllers\cms;

use common\ErrorCode;
use common\SussedCode;
use ff;
use ff\auth\CookieAuthController;
use ff\base\Controller;
use Qiniu\Auth;


/**
 *
 * @name 用户令牌相关
 *
 */

class QiniuController extends CookieAuthController
{

    /**
     *
     * @name  获取七牛token
     * @method GET
     * @format JSON
     * @var string token 用户授权令牌
     * @other 
     * @example
     * [success]JSON:{"code":1,"msg":"Sussed!","data":{"token":"mWklqAuQyEgHH__14LWE2qp0LpZnEn-J7tfeqf5k:8kXWbAuAHx9H_6yaGc2kpAXutg8=:eyJzY29wZSI6InF1d2VpLXZyIiwiZGVhZGxpbmUiOjE2NDY3MjE2NTF9"},"request_params":[]}
     * @author haierspi
     *
     */
    public function actionToken($method = 'GET')
    {

        $time = (int)$this->request->vars['time']; //页码
        $bucket = $this->request->vars['bucket']; //页码
        $key = empty($this->request->vars['key']) ? null: $this->request->vars['key']; //页码



        $accessKey = ff::$config['qiniu']['access_key'];
        $secretKey = ff::$config['qiniu']['secret_key'];
        $bucket = $bucket?$bucket:ff::$config['qiniu']['bucket'];


        $auth = new Auth($accessKey, $secretKey);

        $deadline=time()+$time;

        $opts = array(
            'scope' => $bucket,  
            'deadline' => $deadline,
        );	
        
        $uptoken = $auth->uploadToken($bucket, $key, $deadline, $opts);
    
 
        return SussedCode::SUSSED(
            [
                'uptoken' => $uptoken,
            ]
        );

        
    }






}
