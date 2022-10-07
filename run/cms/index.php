<?php

//项目名称
use ff\helpers\StringLib;
chdir(__DIR__);
define('APP_NAME', 'CMS 内容管理');
//强制前缀访问
define('SYSTEM_CONTROLLERS_PRE', 'cms');
define('SYSTEM_CONTROLLERS_DEFAULT', 'User');





require '../../vendor/autoload.php';
require '../../common/core/FF.php';
$config = require '../../config/config.php';


$Application = new ff\base\Application($config);
$Application->run();
