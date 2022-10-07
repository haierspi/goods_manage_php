<?php

namespace ff\helpers;

class Common
{

    /**
     * 获取客户端IP地址
     * @return void
     * @Author raoxuehui
     */
   public static function getClientIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR')) {
            $client_ip = getenv('REMOTE_ADDR');
        } else {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $client_ip;
    }

    /**
     * 通过子类找所有父类
     * @param $categorys
     * @param $catId
     * @return array
     * @Author raoxuehui
     */
    public static function getCategoryParent($categorys, $catId)
    {
        $parent= [];
        foreach ($categorys as $category) {
            if ($category['cat_id'] == $catId) {
                if ($category['parent_id'] > 0)
                    $parent = array_merge($parent, self::getCategoryParent($categorys, $category['parent_id']));
                $parent[] = $category;
                break;
            }
        }
        return $parent;
    }

    /**
     * 通过父类找所有子类
     * @param $categorys
     * @param int $catId
     * @param int $level
     * @return array
     * @Author raoxuehui
     */
    public static function getCategoryChild($categorys, $catId = 0, $level = 1)
    {
        $child = [];
        foreach ($categorys as $category) {
            if ($category['parent_id'] == $catId) {
                $category['level'] = $level;
                $child[] = $category;
                $child = array_merge($child, self::getCategoryChild($categorys, $category['cat_id'], $level + 1));
            }
        }
        return $child;
    }
}
