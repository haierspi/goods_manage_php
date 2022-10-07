<?php
namespace common\event;

/**
 * 审核通过事件
 * @param
 * @return mixed
 */

class VerifyPass{
    public $event;


    public function __construct($event)
    {
        $this->event = $event;
    }

}