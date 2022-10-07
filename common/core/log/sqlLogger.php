<?php

namespace ff\log;

class sqlLogger
{
    private $handler = null;
    private $module = '';
    public static $key = 'master';

    public function __construct($handler = null)
    {
        $this->__setHandle($handler);
    }

    private function __clone()
    {}

    private function __setHandle(LogHandler $handler)
    {
        $this->handler = $handler;
    }

    public function log($sqls)
    {
        if($sqls && is_array($sqls)){
            foreach ($sqls as $key => $sql) {
                $sqls[$key] = "\r\n/* ".date('Y-m-d H:i:s')." */\r\nEXPLAIN ".$sql.";";
            }
            $logStr = join('', $sqls);
    
            $this->write($logStr);
        }
    }

    protected function write($data)
    {
        $this->handler->write($data);
    }
}
