<?php

namespace ff\base;

use ff;
use ff\log\LogFileHandler;
use ff\log\Logger;
use ff\nosql\redis as Redis;

class ErrorException
{

    const CACHE_KEY = 'ERROR_EXCEPTION_';
    const CACHE_TIME = 3600;

    public static function Error($errno, $errstr, $errfile, $errline)
    {
        $type = 'Error';
        if (defined('SYSTEM_DEBUG_ERROR_COLLECT')) {
            $trace = debug_backtrace();

            $clssName = SELF::class;
            $uniqueKey = ff\helpers\StringLib::getUniqueKey();
            $typeHash = md5($errstr . $errno . $errfile . $errline);

            self::notice($typeHash, $uniqueKey, $errstr, $errno, $errfile, $errline, $type);
            self::log($typeHash, $uniqueKey, $trace, $errstr, $errno, $errfile, $errline, $clssName, $type);
        }

        throw new \ErrorException($errstr, $errno, E_ERROR, $errfile, $errline);
    }

    public static function Exception($ex,$throw = true)
    {
        $type = 'Exception';

        if (defined('SYSTEM_DEBUG_ERROR_COLLECT')) {
            $clssName = get_class($ex);
            $trace = $ex->getTrace();
            $errstr = $ex->getMessage();
            $errno = $ex->getCode();
            $errfile = $ex->getFile();
            $errline = $ex->getLine();
            $uniqueKey = ff\helpers\StringLib::getUniqueKey();
            $typeHash = md5($errstr . $errno . $errfile . $errline);

            self::notice($typeHash, $uniqueKey, $errstr, $errno, $errfile, $errline, $type);
            self::log($typeHash, $uniqueKey, $trace, $errstr, $errno, $errfile, $errline, $clssName, $type);

        }

        if($throw ){
            throw $ex;
        }
        
    }

    public static function log($typeHash, $uniqueKey, $trace, $errstr, $errno, $errfile, $errline, $clssName, $type = 'Exception')
    {

        $logcont = '';
        $logcont .= "ID: <a name=\"$uniqueKey\">" . $uniqueKey . "</a>\n";
        $logcont .= "Type: " . $type . "\n";
        $logcont .= "API: " . ff::$app->router->actionMethod . "\n";
        if (constant('SYSTEM_RUN_MODE') == 'cgi') {
            $logcont .= "RequestMethod: " . ff::$app->router->request->method . "\n";
            $logcont .= "RequestVars: " . json_encode(ff::$app->router->request->vars, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";

            $headerVars = ff::$app->router->request->headerVars;
            unset($headerVars['cookie']);

            $logcont .= "RequestHeaderVars: " . json_encode($headerVars, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";

        } else {
            $logcont .= "RequestCall: " . constant('SYSTEM_RUN_MODE') . ' ' . join(' ', $_SERVER['argv']) . "\n";
        }

        $logcont .= "ErrMsg: " . $errstr . "\n";
        $logcont .= "ErrNo: " . $errno . "\n";
        $logcont .= "ErrFile: " . $errfile . ' ' . $errline . "\n";

        $oneTraceCall = "\n";
        $i = 0;

        if (is_array($trace)) {
            foreach ($trace as $key => $oneTrace) {

                if ($oneTrace['class'] == SELF::class) {
                    unset($trace[$key]);
                    continue;
                } else {

                    $oneTraceCall .= $i . ' ';

                    if ($oneTrace['file']) {
                        $oneTraceCall .= $oneTrace['file'] . " " . $oneTrace['line'] . " ";
                    }

                    if ($oneTrace['class']) {
                        $oneTraceCall .= $oneTrace['class'] . '' . $oneTrace['type'] . '';
                    }

                    if ($oneTrace['function']) {
                        $oneTraceCall .= $oneTrace['function'];
                    }

                    if ($oneTrace['args']) {
                        $args = [];
                        foreach ($oneTrace['args'] as $k1 => $oneArgs) {
                            if (is_object($oneArgs)) {
                                $args[$k1] = get_class($oneArgs);
                            } elseif (is_array($oneArgs)) {

                                foreach ($oneArgs as $k2 => $oneArg) {
                                    if (is_object($oneArg)) {
                                        $args[$k1][$k2] = '[ Object:' . get_class($oneArg) . ' ]';
                                    } else {
                                        $args[$k1][$k2] = $oneArg;
                                    }
                                }
                            } else {
                                $args[$k1] = $oneArgs;
                            }
                        }

                        $oneTraceCall .= "\n" . SELF::white_space(print_r($args, true), "\t");
                    }
                    $oneTraceCall .= "\n";
                    $i++;
                }
            }
        }

        $logcont .= "Trace: " . SELF::white_space($oneTraceCall, "\t");


        $logHandler =  new ff\log\LogFileHandler(SYSTEM_ROOT_PATH . "/runtime/log/debug_errorexception.log");
        $Logger = Logger::Init($logHandler);
        $Logger->LOG($logcont);

    }

    public static function white_space($string, $whitespace)
    {

        //Create an array from the string, each key having one line

        $string = explode(PHP_EOL, $string);

        //Loop through the array and prepend the whitespace

        foreach ($string as $line => $text) {

            $string[$line] = $whitespace . $text;

        }

        //Return the string

        return (implode(PHP_EOL, $string));

    }

    private static function notice($typeHash, $uniqueKey, $errstr, $errno, $errfile, $errline, $type)
    {

        if (constant('RUNTIME_ENVIROMENT') == 'PRODUCTION') {
            if (Redis::exists(SELF::CACHE_KEY . $typeHash)) {
                return;
            } else {
                Redis::set(SELF::CACHE_KEY . $typeHash, 1);
                Redis::expire(SELF::CACHE_KEY . $typeHash, SELF::CACHE_TIME);
            }
        }else{
            return;
        }

        $content = "OMSAPI {$type}:\n";
        $content .= "ID\t:\t" . $uniqueKey . "\n";
        $content .= "File\t:\t" . $errfile . "\n";
        $content .= "Line\t:\t" . $errline . "\n";
        $content .= "Msg\t:\t" . $errstr . "\n";
        $content .= "\n" . constant('RUNTIME_URL') . '/wiki/errorexception#' . $uniqueKey . "\n";

        $dMessage = new \common\logicalentity\DingTalkGroupMessageLogic('SupplyChainSelfOnlineWarning');

        $isAtAll = false;

        $msgtype = "text";
        $info = [
            "content" => $content,
        ];

        return $dMessage->requestByCurl($msgtype, $info, $isAtAll);

    }

}
