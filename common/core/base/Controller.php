<?php
namespace ff\base;

use ff;
use ff\network\Request;

abstract class Controller
{
    public $request;
    public $view;
    public $defaultAction = 'Index';
    public $runControllerClassName = '';
    public $actionAllowMethods;
    public $routerPath;
    public $authController;
    public $responseEnableApiHeader = TRUE;
    public $responseDisplayAdd = TRUE;
    public $responseDisplayAddRequestVars = TRUE;
    public $responseDisplayAddTime = TRUE;


    public function __construct(Request $request)
    {
        $header = getallheaders();

        if (stripos($header['User-Agent'], 'Firefox')) {
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept,token");
            header('Access-Control-Allow-Origin: *');
            if($_SERVER['REQUEST_METHOD']=='OPTIONS'){
                echo 'OK';die;
            }
        }
        $this->request = $request;

    }
    public function beforeAction()
    {

    }

    public function afterAction()
    {
    }

    public function checkmethod($method)
    {
        $methods = explode('|', $method);
        if (in_array($this->request->method, $methods)) {
            return true;
        } else {
            return false;
        }

    }
    public function __call($name, $arguments) 
    {
        if ($this->authController) {
            if (method_exists( $this->authController, $name)) {
                return $this->authController->$name(...$arguments);
            }
        }
        return;
    }


}
