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
 * @name oss 上传
 *
 */

class OssController extends Controller
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
        $saveDir = $this->request->vars['saveDir'];

        $id = ff::$config['oss']['access_key']; // 请填写您的AccessKeyId。
        $key = ff::$config['oss']['secret_key']; // 请填写您的AccessKeySecret。
        $host = ff::$config['oss']['host'];
        $dir = $saveDir?$saveDir:ff::$config['oss']['default_save_dir']; // 用户上传文件时指定的前缀。

        // $callback_param = array(
        //     'callbackUrl' => $callbackUrl,
        //     'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
        //     'callbackBodyType' => "application/x-www-form-urlencoded",
        // );
        // $callback_string = json_encode($callback_param);

        // $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = 60; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);

        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 10485760*100);
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;

        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = array();
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        //$response['callback'] = $base64_callback_body;
        $response['dir'] = $dir; // 这个参数是设置用户上传文件时指定的前缀。
        $response['access_url_pre'] = ff::$config['oss']['access_url_pre'];
        $response['access_file_url_pre'] = ff::$config['oss']['access_url_pre'].$response['dir'];

        return SussedCode::SUSSED($response);

    }

    private function gmt_iso8601($time)
    {
        return str_replace('+00:00', '.000Z', gmdate('c', $time));
    }

}
