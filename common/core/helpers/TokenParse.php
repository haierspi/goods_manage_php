<?php
namespace ff\helpers;

use ff;

class TokenParse
{

    public static function get($token,$encryptkey = null)
    {
        $auth = json_decode(TokenAuth::authcode($token, 'DECODE', $encryptkey?$encryptkey:ff::$config['encryptkey']),true);
        return [$auth['userid'], $auth['nickname'], $auth['expiration']];
    }

    public static function set($userid, $nickname, $expiration, $encryptkey= null, $pwcode = null)
    {
        $auth = [
            'userid'=> $userid,
            'nickname'=> $nickname,
            'expiration' => $expiration
        ];
        return TokenAuth::authcode(json_encode( $auth ), 'ENCODE', $encryptkey?$encryptkey:ff::$config['encryptkey']);
    }

}
