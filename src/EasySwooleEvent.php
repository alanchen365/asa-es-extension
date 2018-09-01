<?php

namespace AsaEs;

use AsaEs\Config\Router;
use AsaEs\Exception\SystemException;
use AsaEs\Process\Inotify;
use AsaEs\Router\HttpRouter;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\SysConst;
use EasySwoole\Core\Swoole\EventRegister;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use EasySwoole\Core\Swoole\ServerManager;

class EasySwooleEvent
{
    public static function frameInitialize(): void
    {
        // 时区设置
        date_default_timezone_set('Asia/Shanghai');
        // 载入路由
        HttpRouter::getInstance()->set();
        // 载入全部配置文件
        Config::initConf();
        // 异常处理
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER, SystemException::class);
    }

    public static function mainServerCreate(ServerManager $server, EventRegister $register): void
    {
        // 服务热重启
        ProcessManager::getInstance()->addProcess('autoReload', Inotify::class);
    }
}
