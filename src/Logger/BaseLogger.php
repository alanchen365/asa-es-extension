<?php

namespace AsaEs\Logger;

use EasySwoole\Config;
use EasySwoole\Core\AbstractInterface\LoggerWriterInterface;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\SysConst;

class BaseLogger
{
    /**
     * 递归创建目录
     */
    public function createDirectory($dir)
    {
        return  is_dir($dir) or $this->createDirectory(dirname($dir)) and  mkdir($dir, 0777);
    }
}
