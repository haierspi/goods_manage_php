<?php
namespace ff\helpers;

class StringLib
{
    /**
     * 判断是否为手机
     *
     * @return boolean
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2022-07-07
     */
    public static function isMobile()
    {

        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }

        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }

        // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile', 'MicroMessenger');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;

            }

        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }

        return false;

    }

    /**
     * 获取唯一字符串
     *
     * @param string $namespace
     * @return string
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2020-11-26
     */
    public static function getUniqueKey($namespace = '')
    {
        static $guid = '';
        $uniqidStr = strtoupper(uniqid());

        $guid = '{' .
        date('Y-md-') .
        substr($uniqidStr, 0, 4) .
        '-' .
        substr($uniqidStr, 4, 4) .
        '-' .
        substr($uniqidStr, 8, 5) .
            '}';

        return $guid;
    }
    //获取随机字符串
    public static function randString($length = 5, $mod = 'UMN', $starthash = '')
    {
        $hash = $starthash;

        if (preg_match("/U/i", $mod)) {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if (preg_match("/M/i", $mod)) {
            $chars .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if (preg_match("/N/i", $mod)) {
            $chars .= '0123456789';
        }
        $max = strlen($chars) - 1;
        if (PHP_VERSION < '4.2.0') {
            mt_srand((double) microtime() * 1000000);
        }
        for ($i = 0; $i < $length; $i++) {

            $hash .= $chars[mt_rand(0, $max)];
            if ($i == 0) {
                $hash = ($hash[0] == '0') ? '1' : $hash;
            }
        }
        return $hash;
    }

    //双向加密函数
    public static function myEncrypt($string, $action = 'EN', $auth = '')
    {
        $string = strval($string);
        if ($string == '') {
            return '';
        }

        if ($action == 'EN') {
            $strauth = substr(md5($string), 8, 10);
        } else {
            $strauth = substr($string, -10);
            $string = base64_decode(substr($string, 0, strlen($string) - 10));
        }
        $key = md5($strauth . $auth);
        $len = strlen($key);
        $code = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $k = $i % $len;
            $code .= $string[$i] ^ $key[$k];
        }
        $code = ($action == 'DE' ? (substr(md5($code), 8, 10) == $strauth ? $code : null) : base64_encode($code) . $strauth);
        return $code;
    }

    //认证
    public static function getArySign($array, $authkey = 'bigqi_com', $authmode = 'md5')
    {
        global $_SIGNSTR;
        if (is_array($array)) {
            if ($array['sign']) {
                unset($array['sign'], $array['sign_type']);
            }
            ksort($array);
            reset($array);
            $sign = '';
            foreach ($array as $key => $value) {
                if ($value != '') {
                    $sign .= "$key=$value&";
                }
            }
            $_SIGNSTR = substr($sign, 0, -1);
            $sign = substr($sign, 0, -1) . $authkey;

            $authmodeary = array('md5', 'num6');

            $authmode = in_array($authmode, $authmodeary) ? $authmode : current($authmodeary);

            if ($authmode == 'md5') {
                return md5($sign);
            } elseif ($authmode == 'num6') {
                return substr(sprintf("%u", crc32($sign)), 0, 6);
            }

        } else {
            return false;
        }
    }

    //根据字符串生成颜色代码
    public static function strColor($str)
    {
        $strcode = md5($str);
        $hexcode = '';
        for ($i = 0; $i < strlen($strcode); $i++) {
            $hexcode .= base_convert($strcode[$i], 36, 16);
        }
        $colorhexcode = substr($hexcode, 0, 6);
        return '#' . $colorhexcode;
    }

    //获取文件大小
    public static function strBytes($val)
    {
        $val = trim($val);
        $strlen = strlen($val) - 1;
        $last = strtolower($val[$strlen]);
        switch ($last) {
            case 'g':$val *= 1024;
            case 'm':$val *= 1024;
            case 'k':$val *= 1024;
        }
        return $val;
    }

    //获取网络地址内容
    public static function getUrlConent($url, $post = [], $cookie = [], $httpheader = [], &$headers = [], $limit = 0, $ip = '', $timeout = 15, $encodetype = 'URLENCODE')
    {

        $return = '';
        $matches = parse_url($url);
        $scheme = $matches['scheme'];
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : ($scheme == 'http' ? '80' : '');

        $ch = curl_init();
        //$httpheader = array();

        if ($ip) {
            $httpheader[] = "Host: " . $host;
        }
        if ($httpheader) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        }
        curl_setopt($ch, CURLOPT_URL, $scheme . '://' . ($ip ? $ip : $host) . ($port ? ':' . $port : '') . $path);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $files = [];
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($encodetype == 'JSON') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
            } elseif ($encodetype == 'URLENCODE') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
            } else {
                foreach ($post as $k => $v) {
                    if (isset($files[$k])) {
                        $post[$k] = '@' . $files[$k];
                    }
                }
                foreach ($files as $k => $file) {
                    if (!isset($post[$k]) && file_exists($file)) {
                        $post[$k] = '@' . $file;
                    }
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }
        }
        if ($cookie) {
            $cookiestr = '';
            foreach ($cookie as $key => $value) {
                $cookiestr .= "$key=" . urlencode($value) . ";";
            }
            $cookiestr = substr($cookiestr, 0, -1);
            curl_setopt($ch, CURLOPT_COOKIE, $cookiestr);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($errno || $status['http_code'] != 200) {
            return;
        } else {
            $headers = substr($data, 0, $status['header_size']);
            $data = substr($data, $status['header_size']);
            return !$limit ? $data : substr($data, 0, $limit);
        }
    }

    public static function getUrlJosnConent(...$args)
    {
        $result = call_user_func_array([StringLib::class, 'getUrlConent'], $args);
        return json_decode($result);
    }
    /**
     * Returns the trailing name component of a path.
     * This method is similar to the php function `basename()` except that it will
     * treat both \ and / as directory separators, independent of the operating system.
     * This method was mainly created to work on php namespaces. When working with real
     * file paths, php's `basename()` should work fine for you.
     * Note: this method is not aware of the actual filesystem, or path components such as "..".
     *
     * @param string $path A path string.
     * @param string $suffix If the name component ends in suffix this will also be cut off.
     * @return string the trailing name component of the given path.
     * @see http://www.php.net/manual/en/function.basename.php
     */
    public static function basename($path, $suffix = '')
    {
        if (($len = mb_strlen($suffix)) > 0 && mb_substr($path, -$len) === $suffix) {
            $path = mb_substr($path, 0, -$len);
        }
        $path = rtrim(str_replace('\\', '/', $path), '/\\');
        if (($pos = mb_strrpos($path, '/')) !== false) {
            return mb_substr($path, $pos + 1);
        }

        return $path;
    }

    /**
     * 下划线转驼峰
     *
     * @param [type] $uncamelized_words
     * @param string $separator
     * @return void
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2020-06-04
     */
    public static function camelize($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    /**
     * 驼峰命名转下划线命名
     *
     * @param [type] $camelCaps
     * @param string $separator
     * @return void
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2020-06-04
     */
    public static function uncamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

    /*
     * 下划线转驼峰
     */
    public static function convertUnderline($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);
        return $str;
    }

    /*
     * 驼峰转下划线
     */
    public static function humpToLine($str)
    {
        $str = str_replace("_", "", $str);
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, $str);
        return ltrim($str, "_");

    }
}
