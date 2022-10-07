<?php
namespace ff\helpers;

class Crypt
{
    //base64_encode(openssl_random_pseudo_bytes(32))生成
    private static $key = '+Cm0/dhmcYl7Gko/r+V+pP63RVpeSBX+yn9TB6eAsWo=';
    //base64_encode(openssl_random_pseudo_bytes(16))生成
    private static $iv = 'cR/27pU5TnPAKuNtvV0jdA==';

    /**
     * 加密数据
     * @param string $data 需要加密的数据
     * @return string
     * @author Roller
     * @date   2018/8/2 12:00
     */
    public static function encrypt($data)
    {
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', base64_decode(self::$key), OPENSSL_RAW_DATA, base64_decode(self::$iv));
        return self::base64ToUrl($encrypted);
    }

    /**
     * 解密
     * @param string $encrypted 待解密数据
     * @return string
     * @author Roller
     * @date   2018/8/2 12:00
     */
    public static function decrypt($encrypted)
    {
        $encrypted = self::urlToBase64($encrypted);
        return openssl_decrypt($encrypted, 'aes-256-cbc', base64_decode(self::$key), OPENSSL_RAW_DATA, base64_decode(self::$iv));
    }

    /**
     * base64 转url
     * @param $string
     * @return mixed|string
     * @author Roller
     * @date   2018/8/2 14:08
     */
    private static function base64ToUrl($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * url 转base64
     * @param $string
     * @return bool|string
     * @author Roller
     * @date   2018/8/2 14:08
     */
    private static function urlToBase64($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}
