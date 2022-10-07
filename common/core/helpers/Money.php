<?php

namespace ff\helpers;

class Money{
    public static function add($n1,  $n2, $scale = '0'){
        return  bcadd($n1, $n2, $scale);
    }

    public static function subtract($n1, $n2, $scale = '0'){
        return bcsub($n1, $n2, $scale);
    }
    public static function multiply($n1, $n2, $scale = '0'){
        return bcmul($n1, $n2, $scale);
    }
    public static function divide($n1, $n2, $scale = '0'){
        return bcdiv($n1, $n2,$scale);
    }
}