<?php

namespace ff\event;

use common\event\Updating;
use common\listeners\Test as tests;
use ff\base\Component;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use ff;
class event extends Component
{

    public $event;
    public function __construct()
    {
     //   $this->event = ff::$app->container->make('events');

       // $this->register();
    }

    /**
     * 注册事件
     * @param
     * @return mixed
     */
    public function register(){
        $this->event->listen(Updating::class,tests::class);
    }
}