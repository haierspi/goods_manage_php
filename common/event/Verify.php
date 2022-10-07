<?php
namespace common\event;

/**
 * 审核事件
 * @param 
 * @return mixed
 */

class Verify{
    public $event;


    public function __construct($event)
    {
        $this->event = $event;



    }

}