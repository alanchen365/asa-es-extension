<?php

namespace AsaEs\Logger;

use AsaEs\Utility\Time;
use EasySwoole\Config;
use EasySwoole\Core\AbstractInterface\LoggerWriterInterface;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\SysConst;

class FileLogger extends BaseLogger
{
    use Singleton;

    private $loggerWriter;
    private $defaultDir;

    public function __construct()
    {
        $logger = Di::getInstance()->get(SysConst::LOGGER_WRITER);
        if ($logger instanceof LoggerWriterInterface) {
            $this->loggerWriter = $logger;
        }
        $this->defaultDir = Config::getInstance()->getConf('LOG_DIR');
    }

    public function log($data, $category = 'default'):FileLogger
    {
        if(!is_string($data)){
            $data = json_encode($data);
        }

        // 时间处理
        $ym = date('ym');
        $d = date('d', time());

        // 是否是json
        $jsonArray = json_decode($data,true);

        if(json_last_error() != JSON_ERROR_NONE){
            // 如果不是json, 就单独处理
            $data = [
                'create_time' => Time::getNowDataTime(),
                'string' => $data,
            ];
            $data = json_encode($data);
        }

        // 换行
        $data = $data."\n";
        $filePath = $this->defaultDir . "/{$category}/";
        $fileName = $filePath. "/{$ym}{$d}.log";

        clearstatcache();
        if(!is_dir($filePath)){
            $this->createDirectory($filePath);
        }
        file_put_contents($fileName, $data, FILE_APPEND|LOCK_EX);
        return $this;
    }

    public function console(string $str, $saveLog = 1)
    {
        echo $str . "\n";
        if ($saveLog) {
            $this->log($str, 'console');
        }
    }

    public function consoleWithTrace(string $str, $saveLog = 1)
    {
        $debug = $this->debugInfo();
        $debug = "file[{$debug['file']}] function[{$debug['function']}] line[{$debug['line']}]";
        $str = "{$debug} message: [{$str}]";
        echo $str . "\n";
        if ($saveLog) {
            $this->log($str, 'console');
        }
    }

    public function logWithTrace(string $str, $category = 'default')
    {
        $debug = $this->debugInfo();
        $debug = "file[{$debug['file']}] function[{$debug['function']}] line[{$debug['line']}]";
        $this->log("{$debug} message: [{$str}]", $category);
    }

    private function debugInfo()
    {
        $trace = debug_backtrace();
        $file = $trace[1]['file'];
        $line = $trace[1]['line'];
        $func = isset($trace[2]['function']) ? $trace[2]['function'] : 'unKnown';
        return [
            'file'=>$file,
            'line'=>$line,
            'function'=>$func
        ];
    }
}
