<?php
namespace common\providers;
use common\exception\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class ExceptionServiceProvider extends ServiceProvider {
    public function boot()
    {

        $this->app->singleton(
            ExceptionHandler::class,
            Handler::class
        );
    }
}