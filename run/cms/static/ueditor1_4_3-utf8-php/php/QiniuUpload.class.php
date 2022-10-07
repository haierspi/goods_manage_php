<?php
require_once '../../../../../vendor/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class QiniuUpload
{


    // const ACCESS_KEY = 'qYGSIXMixYnwzfsVOa_6zrzY0jr-sq-4Gk0GFOht';
    // const SECRET_KEY = 'ETyLObnbODG92dN-tefwmwK7qKo0DfNlQO1zF-ir';
    // private $bucket = 'imagecache';
    // private $domain = 'http://imagecache.pzlife.vip/';


    const ACCESS_KEY = 'mWklqAuQyEgHH__14LWE2qp0LpZnEn-J7tfeqf5k';
    const SECRET_KEY = '_xVXh6K1tptMm6SCDGDsgg8EAi0GTlf3Sel52_eZ';
    private $bucket = 'quwei-vr';
    private $domain = 'https://quwei-vr.shangweitech.com/';




    private $auth;
    private $uploadMgr;

    public function __construct(){
        $this->auth = new Auth(self::ACCESS_KEY, self::SECRET_KEY);
        $this->uploadMgr = new UploadManager();
    }

    public function getUploadToken($key=null){
        $token = $this->auth->uploadToken($this->bucket);
        return $token;
    }

    public function uploadFile($key, $filePath){
        if(!$key || !$filePath) return false;
        $token = $this->getUploadToken($key);



        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();

       // ddl($token, $key, $filePath);
        list($ret, $err) = $this->uploadMgr->putFile($token, $key, $filePath);

        $filepath = '';
        if ($err === null) {
            $filepath = $this->domain . $ret['key'];
        }

        return [$filepath, $err];
    }

    public function uploadFileData($key, $data){
        if(!$key || !$data) return false;
        $token = $this->getUploadToken($key);
        list($ret, $err) = $this->uploadMgr->putFile($token, $key, $data);
        if ($err !== null) {
            return false;
        }
        return $this->domain . $ret['key'];
    }
}