<?php
namespace ff\helpers;

class Cookie
{


    public static function setCookie($var, $value = '', $life = 0,$pre = '')
    {

        $var = $pre . $var;

        if ($value == '' || $life < 0) {
            $value = '';
            $life = -1;
        }

        $httponly = true;

        $life = $life > 0 ? TIMESTAMP + $life : ($life < 0 ? TIMESTAMP - 31536000 : 0);
        $path = '/';
        $secure = $_SERVER['HTTPS'] == 'on' ? 1 : 0;


        setcookie($var, $value, $life, $path, '' , $secure, $httponly);

    }


    public static function getCookie($var,$pre = '')
    {
        $var = $pre.$var;
        return $_COOKIE[$var];
    }
}
