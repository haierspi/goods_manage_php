<?php
namespace models\tables;

use common\Admin;
use ff\database\Model;
use ff\helpers\TokenParse;

class AdminUserModel extends Model
{

    public $table = 'admin_user';
    protected $primaryKey = 'auid';
    public $timestamps = false;
    private $authKey = 'yqqdRs$=rx[-';

    const TOKEN_EXPIRATION = 86400;

    public function getUserByField($username, $field)
    {
        return $this->where($field, $username)
            ->limit(1)
            ->first();
    }

    public function checkPassword($password, $verifyPw)
    {
        $calcPw = md5(sha1($password) . $this->authKey);
        return $calcPw === $verifyPw;
    }

    public function getCalcPw($password)
    {
        return  md5(sha1($password) . $this->authKey);
    }

    // 获取并更新token
    public function getToken()
    {

        $token = TokenParse::set($this->auid, $this->username, TIMESTAMP + SELF::TOKEN_EXPIRATION, Admin::ENCRYPT_KEY);

        $this->token =  $token;
        $this->save();

        return $token;
    }
}
