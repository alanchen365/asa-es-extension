<?php

namespace AsaEs\Logger;

use AsaEs\Utility\StringUtility;
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

    /**
     * 日志统一写文件
     * @param $data
     * @param string $category
     * @return FileLogger
     */
    public function log($data, $category = 'default'):FileLogger
    {
        // 日志分类名称 驼峰转下划线 转小写
        $category = strtolower($category);

        // 构造日志bean
        $fileLogBean = new FileLogBean();

        $fileLogBean->setFileBasePath($this->defaultDir);
        $fileLogBean->setCategory($category);
        $fileLogBean->setFileName('Ymd');

        $fileLogBean->setCreateTime(Time::getNowDataTime());
        $fileLogBean->setMessage($data);

        $logString = $fileLogBean->__toString()."\n";
        $filePath =  $fileLogBean->getFileBasePath() . '/' . $fileLogBean->getCategory();
        $fileFullName = $filePath . '/' . $fileLogBean->getFileName();

        clearstatcache();
        if(!is_dir($filePath)){
            $this->createDirectory($filePath);
        }
        file_put_contents($fileFullName, $logString, FILE_APPEND|LOCK_EX);
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
