<?php
namespace ff;

define('SYSTEM_IN', true);
define('SYSTEM_DEBUG', E_ALL & ~E_NOTICE &~E_WARNING);
if(!defined('SYSTEM_DEBUG') ){
   define('SYSTEM_DEBUG', E_ALL & ~E_NOTICE &~E_WARNING );
}


define('SYSTEM_RUN_MODE', (PHP_SAPI === 'cli') ? 'cli' : 'cgi');

defined('SYSTEM_CORE_PATH') || define('SYSTEM_CORE_PATH', __DIR__);
defined('SYSTEM_CODE_MSG_DISPLAY') || define('SYSTEM_CODE_MSG_DISPLAY', 1);

defined('SYSTEM_ROOT_PATH') || define('SYSTEM_ROOT_PATH', substr(constant('SYSTEM_CORE_PATH'), 0, -12));
defined('SYSTEM_COMMON_PATH') || define('SYSTEM_COMMON_PATH', constant('SYSTEM_ROOT_PATH') . '/common');
defined('SYSTEM_CONFIG_PATH') || define('SYSTEM_CONFIG_PATH', constant('SYSTEM_ROOT_PATH') . '/config');
defined('SYSTEM_CONTROLLERS_PATH') || define('SYSTEM_CONTROLLERS_PATH', constant('SYSTEM_ROOT_PATH') . '/controllers');
defined('SYSTEM_MODELS_PATH') || define('SYSTEM_MODELS_PATH', constant('SYSTEM_ROOT_PATH') . '/models');
defined('SYSTEM_VIEWS_PATH') || define('SYSTEM_VIEWS_PATH', constant('SYSTEM_ROOT_PATH') . '/views');
defined('SYSTEM_RUNTIME_PATH') || define('SYSTEM_RUNTIME_PATH', constant('SYSTEM_ROOT_PATH') . '/runtime');
defined('SYSTEM_BEGIN_TIME') || define('SYSTEM_BEGIN_TIME', microtime(true));

//调用连接
defined('RUNTIME_URL');

//默认控制器缺省目录
defined('SYSTEM_CONTROLLERS_PRE') || define('SYSTEM_CONTROLLERS_PRE', '');

//默认控制器
defined('SYSTEM_CONTROLLERS_DEFAULT') || define('SYSTEM_CONTROLLERS_DEFAULT', '');


//程序错误日志收集
defined('SYSTEM_DEBUG_ERROR_COLLECT');

//SQL执行日志收集
defined('SYSTEM_DEBUG_SQLLOG');
defined('SYSTEM_DEBUG_SQLLOG_FILE');




define('SYSTEM_TABLE_EXTENDABLE', false);

define('RUNTIME_ENVIROMENT', getenv('RUNTIME_ENVIROMENT') ? getenv('RUNTIME_ENVIROMENT') : (isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : null));
define('CHARSET', 'utf-8');
define('PATHSEPARATOR', '/');
define('PHPEXT', '.php');
define('TIMESTAMP', time());
define('DATETIME', date('Y-m-d H:i:s', TIMESTAMP));

defined('SYSTEM_DEBUG') && error_reporting(constant('SYSTEM_DEBUG'));

if (constant('SYSTEM_RUN_MODE') == 'cgi') {
    define('RUNTIME_HTTP_HOST', (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
}

if (constant('SYSTEM_RUN_MODE') == 'cgi') {
    define('RUNTIME_ENTRY_POINT', constant('RUNTIME_HTTP_HOST') . $_SERVER['REQUEST_URI']);
} else {
    define('RUNTIME_ENTRY_POINT', constant('SYSTEM_RUN_MODE') . ' ' . join(' ', $_SERVER['argv']));
}

//SQL尾巴
define('SQL_TAIL', "\n/*> " . constant('RUNTIME_ENTRY_POINT') . " <*/");

if (function_exists('date_default_timezone_set')) {
    @date_default_timezone_set('PRC');
}
class base
{
    const DB_FINDALL_ASARRAY = 1; //数据强制全部返回数组
    public static $config;
    public static $app;
    public static $network;
    public static $force_outputformat = false; //强制输出格式
    public static $container;
    public static $classMap = [];
    public static $aliases = [
        '@ff' => SYSTEM_CORE_PATH,
        '@common' => SYSTEM_COMMON_PATH,
        '@controllers' => SYSTEM_CONTROLLERS_PATH,
        '@models' => SYSTEM_MODELS_PATH,
    ];
    public static function autoload($className)
    {

        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            $classFile = static::getAlias($classFile);
        } elseif (strpos($className, '\\') !== false) {
            $classFile = static::getAlias('@' . str_replace('\\', '/', $className) . PHPEXT, false);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } elseif (is_file($classFile = constant('SYSTEM_COMMON_PATH') . PATHSEPARATOR . $className . PHPEXT)) {

        } else {
            return;
        }
        include $classFile;

        if (SYSTEM_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new \Exception("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }

    public static function getAlias($alias, $throwException = true)
    {
        if (strncmp($alias, '@', 1)) {
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
        }

        if ($throwException) {
            throw new \Exception("Invalid path alias: $alias");
        } else {
            return false;
        }
    }

    public static function createObject($className, array $params = [])
    {
        return static::$container->get($className, $params);
    }

    public static function strBytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':$val *= 1024;
            case 'm':$val *= 1024;
            case 'k':$val *= 1024;
        }
        return $val;
    }
}
