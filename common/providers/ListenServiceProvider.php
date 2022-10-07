<?php
namespace common\providers;




use common\event\Creating;
use common\event\Verify;
use common\event\VerifyPass;
use common\listeners\CreatingListeners;
use common\listeners\Test;
use common\listeners\UpdatingListeners;
use common\listeners\VerifyListeners;
use common\listeners\VerifyPassListeners;
use Illuminate\Support\ServiceProvider;

class ListenServiceProvider extends ServiceProvider {

    protected $listen = [
        \common\event\Updating::class=> [
            UpdatingListeners::class,
        ],///数据库操作事件

        Verify::class=>[
            VerifyListeners::class
        ],

        VerifyPass::class=>[
            VerifyPassListeners::class
        ],
        Creating::class=>[
            CreatingListeners::class
        ]

    ];
    public function boot()
    {
        $this -> dispatcher = $this -> app -> make('events');
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this -> dispatcher -> listen($event,$listener);
            }
        }
    }

    public function register(){


    }
}