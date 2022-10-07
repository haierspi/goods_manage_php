<?php
namespace ff\helpers;

class arrayLib
{
    /**
     * 判断数组是否是自然数组
     *
     * @param  array $arr
     * @return boolean
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2020-11-20
     */
    public static function isAssoc($arr)
    {
        return array_keys($arr) === range(0, count($arr) - 1);
    }

    /**
     * 将数组连接成带引号字符串
     *
     * @param array $arr
     * @return void
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2020-11-20
     */
    public static function join($arr, $gapStr = ',')
    {
        return implode($gapStr, array_map(function ($str) {
            return sprintf("'%s'", $str);
        }, $arr));
    }

    /**
     * 合并两个数组并将索引相同的float值累加
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2020-08-13
     */
    public static function arrayValueSumMerge(array $array1, array $array2)
    {
        if (is_array($array1) && is_array($array2)) {
            $arrayMerge = array();
            foreach ($array2 as $key => $value) {
                if (array_key_exists($key, $array1) && is_float($array1[$key])) {
                    $arrayMerge[$key] = $value + $array1[$key];
                    unset($array1[$key]);
                } else {
                    $arrayMerge[$key] = $value;
                }
            }

            return array_merge($array1, $arrayMerge);
        } else {
            return false;
        }
    }


    /**
     * 
     * 将子数组求和并返回到主数据内
     * 
     * @param array $mainArray 主数组
     * @param array $subArray 子数组
     * @param array .....$fields 查询字段
     * @return array
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2021-03-18
     */
    public static function subArrayValueSum()
    {

        $params = func_get_args();

        $mainArray = isset($params[0]) ? $params[0] : [];
        $subArray = isset($params[1]) ? $params[1] : [];

        $fields = array_slice($params,2);

        if (is_array($mainArray) && is_array($subArray) && is_array($fields)) {
            $collectionAry = array();
            foreach ($subArray as $oneSubArray) {
                foreach ($fields as $field) {
                    $collectionAry[$field] += (float) $oneSubArray[$field];
                }
            }
            return array_merge($mainArray, $collectionAry);
        } else {
            return false;
        }
    }

    /**
     * 将数组转换成对象
     *
     * @param array $arr
     * @return object
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2020-11-20
     */
    public static function arrayToObject($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' ) {
                if (SELF::isAssoc($v)) {
                    foreach ($v as $k1 => $v1) {
                        $arr[$k][$k1] = (object) SELF::arrayToObject($v1);
                    }
                }else{
                    $arr[$k] = (object) SELF::arrayToObject($v);
                }
            }
        }
        return (object) $arr;
    }

    public static function objectToArray($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)SELF::objectToArray($v);
            }
        }
     
        return $obj;
    }

    /**
     * 根据某个字段 二位数组去重 
     *
     * @param array $array
     * @param string $field
     * @return array
     * @Author raoxuehui
     * @DateTime 2021-07-08
     */
    public static function arrayUnique($array, $field)
    {
        $arr = array();
        foreach ($array as $val) {
            $tmep = false;
            foreach ($arr as $_val) {
                if ($_val[$field] == $val[$field]) {
                    $tmep = true;
                    break;
                }
            }
            if (!$tmep) {
                $arr[] = $val;
            }
        }
        return $arr;
    }
}
