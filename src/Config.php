<?php

namespace AsaEs;

use AsaEs\Config\Router;
use AsaEs\Router\HttpRouter;
use AsaEs\Utility\Env;
use EasySwoole\Core\Utility\File;

class Config
{
    /**
     * 获取配置文件
     */
    public static function getConf(string $keyPath, bool $env = false)
    {
        // 获取当前开发环境
        if ($env) {
            $keyPath = $keyPath . "." . Env::get();
        }
        return \EasySwoole\Config::getInstance()->getConf($keyPath);
    }

    /**
     * 初始化配置文件
     */
    public static function initConf()
    {
        $Conf = \EasySwoole\Config::getInstance();
        $files = File::scanDir(EASYSWOOLE_ROOT.'/Conf');
        foreach ($files as $file) {
            $data = require_once $file;
            $Conf->setConf(strtolower(basename($file, '.php')), (array) $data);
        }
    }
}
