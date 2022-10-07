<?php

namespace ff\log;

class LogFileHandler implements LogHandler
{
    private $handle = null;

    public function __construct($file = '')
    {

        $this->handle = fopen($file, 'c+');

        if (shell_exec('whoami') == posix_getpwuid(fileowner($file))) {
            @chmod($file, 0777);
        }

    }

    public function write($msg)
    {

        $line = 0;
        $fcontent = '';

        while($fcontent .= stream_get_line($this->handle,8192,"\n")){ 
            $line++; 
            if($line>100){
                break;
            }
        } 


        // while (!feof($this->handle)) {
        //     //每次读取2M
        //     while($fcontent .= stream_get_line($this->handle,8192,"\n")){ 
        //         $line++; 
                
        //     } 
        //     if ($content = fread($this->handle, 1024 * 1024 * 2)) {

        //         $fcontent .= $content;
        //         //计算读取到的行数
        //         $num = substr_count($content, "\n");
        //         $line += $num;
        //     }
        //     if($line>500){
        //         break;
        //     }
        // }


        $msg = str_replace("\n", "\$\$LF\$\$", $msg) . "\n".$fcontent ;
        rewind($this->handle);
        fwrite($this->handle, $msg);
    }

    public function read()
    {
        $contents = '';
        fseek($this->handle, 0);
        while (!feof($this->handle)) {
            $content = fread($this->handle, 8192);
            $contents .= str_replace("\$\$LF\$\$", "\n", $content);
        }
        return $contents;
    }

    public function __destruct()
    {
        fclose($this->handle);
    }
}
