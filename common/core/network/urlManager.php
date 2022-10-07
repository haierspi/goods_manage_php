<?php
namespace ff\network;

use AlibabaCloud\ROS\V20150901\ContinueCreateStack;
use AlibabaCloud\SDK\Dingtalk\Vcrm_1_0\Models\GetOfficialAccountContactsResponseBody\values\contacts;
use ff\base\Component;

class urlManager
{
    private $ruleVars = [];
    private $config = [];
    public function __construct($config)
    {
        $this->config = $config;

    }
    public function init($requestPath)
    {
        $this->ruleVars = [];
        $urldata = parse_url($requestPath);


        preg_match('/^((\/(v[\d\.]+))?([\w\-\/]+))(\.(\w+))?$/is', $urldata['path'], $match);

        $this->ruleVars['_FORMAT'] = isset($match[6])? $match[6] :'';

        $pathVars = explode('/', substr($match[1], 1));

        if (count($pathVars) == 1) {
            $pathVars[] = '';
        }

        $pathSlicelength = count($pathVars) < 3 ? count($pathVars) : 3;

        for ($pathSlicelength = $pathSlicelength; $pathSlicelength > 0; $pathSlicelength--) {

            $pathSliceVars = array_slice($pathVars, 0, $pathSlicelength);

            $controllerVars = array_slice($pathVars, 0, $pathSlicelength - 1);

            $pathVersion = '';
            if ($pathSlicelength == 3) {
                $pathVersion = $controllerVars[0];
                $controllerVars[0] = str_replace('.', '_', $controllerVars[0]);
            }
            $controllerBefore = end($controllerVars);

            $lastIndex = count($controllerVars) - 1;
            $controller = ucfirst($controllerBefore);
            $controllerVars[$lastIndex] =  $controller;

            $controllerPre = defined('SYSTEM_CONTROLLERS_PRE')? constant('SYSTEM_CONTROLLERS_PRE'):'';
            $controllerFile = SYSTEM_CONTROLLERS_PATH . PATHSEPARATOR .$controllerPre.PATHSEPARATOR. join(PATHSEPARATOR, $controllerVars) . 'Controller' . PHPEXT;


            if (!$this->fileExists($controllerFile)) {
                $controllerVars[$lastIndex] =  $controllerBefore;
                $controller = $controllerBefore;

                $controllerPre = defined('SYSTEM_CONTROLLERS_PRE')? constant('SYSTEM_CONTROLLERS_PRE'):'';
                $controllerFile = SYSTEM_CONTROLLERS_PATH . PATHSEPARATOR .$controllerPre.PATHSEPARATOR. join(PATHSEPARATOR, $controllerVars) . 'Controller' . PHPEXT;
            }
            

 


            if ($this->fileExists($controllerFile)) {
                $this->ruleVars['_VERSION'] = $pathVersion;
                $this->ruleVars['_CONTROLLER'] = $controller;
                $this->ruleVars['_ACTION'] = end($pathSliceVars);
                $this->ruleVars['_ACTION_PARAMS'] = array_slice($pathVars, $pathSlicelength);
                break;
            }

        }


        if(!isset($this->ruleVars['_CONTROLLER'])){
            $this->ruleVars['_CONTROLLER'] = constant('SYSTEM_CONTROLLERS_DEFAULT');
        }



    }

    public function __get($name)
    {
        if (!isset($this->ruleVars[$name])) {
            throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name, 0);
        }
        return $this->ruleVars[$name] ?: null;
    }
    public function __isset($name)
    {
        return isset($this->ruleVars[$name]);
    }

    public function fileExists($file)
    {
        return file_exists($file);
    }
}
