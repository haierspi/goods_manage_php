<?php

namespace ff\log;


class LogRedisHandler implements LogHandler
{
	private $handle = null;
	
	public function __construct($file = '')
	{
		$this->handle = fopen($file,'a');
		@chmod($file, 0777); 
	}
	
	public function write($msg)
	{
		fwrite($this->handle, $msg);
	}
	
	public function __destruct()
	{
		fclose($this->handle);
	}
}