<?php
namespace ff\base;

use common\providers\ExceptionServiceProvider;
use common\providers\ListenServiceProvider;
use ff;
use ff\database\db;
use ff\network\Request;
use ff\network\WebService;
use ff\view\template;
use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Log\LogServiceProvider;
use Throwable;

class Application extends Container
{

    public function __construct($config)
    {
        ff::$config = $config;
        ff::$app = new \stdClass;

        static::setInstance($this);
        $this->instance('app', $this);

        $this->instance(Container::class, $this);
        $this->registerCoreContainerAliases();
        $this->registerServiceProvider();

        $this->initComponents();
    }

    private function initComponents()
    {

        if (!isset(ff::$config['components'])) {
            return;
        }

        foreach (ff::$config['components'] as $appComKey => $appComConf) {
            $classname = $appComConf['class'];
            if (is_subclass_of($classname, 'ff\base\Componentif')) {
                unset($appComConf['class']);
                ff::$app->$appComKey = ff::createObject($classname, $appComConf);
            }
        }

    }

    /* 业务级 核心方法 */

    public function run()
    {

        ff::$app->router = ff::createObject('ff\network\Router', ['ff\network\Request', 'ff\network\Response', constant('SYSTEM_RUN_MODE') == 'cli' ? (ff\network\Router::MODE_CLI) : (ff\network\Router::MODE_CGI)]);
        ff::$app->router->init();
        ff::$app->router->runController();

    }

    // 以服务的方式启动
    public function service(\Swoole\Http\Request $swRequest, \Swoole\Http\Response $swResponse)
    {
        ff::$app->router = ff::createObject('ff\network\Router', ['ff\network\Request', 'ff\network\Response', ff\network\Router::MODE_SWOOLE]);

        ff::$app->router->init($swRequest, $swResponse);
        return ff::$app->router->runController();
    }

    public function registerServiceProvider()
    {

        $service_provider = [

            EventServiceProvider::class,
            /////////

            ListenServiceProvider::class, //事件提供者
            DatabaseServiceProvider::class,
            ExceptionServiceProvider::class,
            LogServiceProvider::class,

            ///////第三方组件

        ];
        foreach ($service_provider as $provider) {
            if (method_exists($provider = new $provider(Application::getInstance()), 'register')) {
                $provider->register(); ///注册
            }
            if (method_exists($provider, 'boot')) {
                $provider->boot(); ///初始化
            }
        }

        return;

    }

    public function registerCoreContainerAliases()
    {
        foreach ([
            'app' => [self::class, \Illuminate\Contracts\Container\Container::class, \Illuminate\Contracts\Foundation\Application::class, \Psr\Container\ContainerInterface::class],
            'log' => [\Illuminate\Log\LogManager::class, \Psr\Log\LoggerInterface::class],
        ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    public function storagePath()
    {
        return getcwd() . '/../runtime';
    }

}
