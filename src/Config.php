<?php

namespace AsaEs;

use AsaEs\Config\Router;
use AsaEs\Router\HttpRouter;
use AsaEs\Utility\Env;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Utility\File;

class Config
{
    use Singleton;

    /**
     * 获取配置文件
     */
    public function getConf(string $keyPath, bool $env = false)
    {
        // 获取当前开发环境
        if ($env) {
            $keyPath = $keyPath . "." . Config::getEnv();
        }
        return \EasySwoole\Config::getInstance()->getConf($keyPath);
    }

    /**
     * 初始化配置文件
     */
    public function register()
    {
        $Conf = \EasySwoole\Config::getInstance();
        $files = File::scanDir(EASYSWOOLE_ROOT.'/Conf');
        foreach ($files as $file) {
            $data = require_once $file;
            $Conf->setConf(strtolower(basename($file, '.php')), (array) $data);
        }
    }

    /**
     * 开发环境 本机(LOCAL) 开发服务器(DEVELOP) 测试服务器(TESTING) 生产服务器(PRODUCTION)
     * @return string
     */
    public function getEnv():string
    {
        return strtoupper(\EasySwoole\Config::getInstance()->getConf('ENV'));
    }

    /**
     * 获取配置文件中数据库名称
     */
    public function getDbName(string $diName = AsaEsConst::DI_MYSQL_DEFAULT, string $configFile = 'mysql', bool $env = true)
    {
        // 获取当前开发环境
        if ($env) {
            $keyPath = $configFile . "." . Config::getEnv() . "." . $diName . "." . "db";
        }
        return strtolower(\EasySwoole\Config::getInstance()->getConf($keyPath));
    }
}
