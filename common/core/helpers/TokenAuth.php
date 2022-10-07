<?php
namespace ff\helpers;

class TokenAuth 
{
	static function xor_enc($str,$key) {
        $str = array_values(unpack('n*', iconv('gbk', 'ucs-2', $str)));
        $key = array_values(unpack('n*', iconv('gbk', 'ucs-2', $key)));
        $crytxt = '';
        $keylen = count($key);
        foreach($str as $i=>$v) {   
          $k = $i % $keylen;
          $crytxt .= pack('n', $v ^ $key[$k]);
        }
        return iconv('ucs-2', 'gbk', $crytxt); 
    }

    static function encode($response,$key)
    {
        $response = base64_encode($response);
        $response = self::xor_enc($response,'233');
        return $response;
    }

    static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0){
      if($key==''){
        $key='nessland';
      }
      if($operation == 'DECODE') {
        $string = str_replace('[a]','+',$string);
        $string = str_replace('[b]','&',$string);
        $string = str_replace('[c]','/',$string);
      }
      $ckey_length = 4;
      $key = md5($key ? $key : 'windlaputa');
      $keya = md5(substr($key, 0, 16));
      $keyb = md5(substr($key, 16, 16));
      $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
      $cryptkey = $keya.md5($keya.$keyc);
      $key_length = strlen($cryptkey);
      $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
      $string_length = strlen($string);
      $result = '';
      $box = range(0, 255);
      $rndkey = array();
      for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
      }
      for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
      }
      for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
      }
      if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
  
          return substr($result, 26);
        } else {
          return '';
        }
      } else {
        $ustr = $keyc.str_replace('=', '', base64_encode($result));
        $ustr = str_replace('+','[a]',$ustr);
        $ustr = str_replace('&','[b]',$ustr);
        $ustr = str_replace('/','[c]',$ustr);
        return $ustr;
      }
    }
}