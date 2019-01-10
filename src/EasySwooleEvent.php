<?php

namespace AsaEs;

use App\AppConst\AppInfo;
use App\EsCrontab;
use AsaEs\Config\Router;
use AsaEs\Exception\Service\MiddlewareException;
use AsaEs\Exception\Service\SignException;
use AsaEs\Exception\SystemException;
use AsaEs\Logger\AccessLogger;
use AsaEs\Logger\FileLogger;
use AsaEs\Middleware\AccessLog;
use AsaEs\Middleware\Dispatch;
use AsaEs\Middleware\EmptyParamFilter;
use AsaEs\Process\Inotify;
use AsaEs\Process\Timer;
use AsaEs\Router\HttpRouter;
use AsaEs\Utility\ArrayUtility;
use EasySwoole\Core\Component\Crontab\CronTab;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Component\SysConst;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Swoole\EventRegister;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use EasySwoole\Core\Swoole\ServerManager;
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;

class EasySwooleEvent
{
    public static function frameInitialize(): void
    {
        // 时区设置
        date_default_timezone_set('Asia/Shanghai');
        // 注册路由
        if (Config::getInstance()->getConf('ROUTER')) {
            HttpRouter::getInstance()->registered();
        }
        // 注册配置文件
        Config::getInstance()->register();
        // 注册异常
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER, SystemException::class);
    }
    
    public static function mainServerCreate(ServerManager $server, EventRegister $register): void
    {
        // 服务热重启
        ProcessManager::getInstance()->addProcess(AsaEsConst::PROCESS_AUTO_RELOAD, Inotify::class);
        // 进程批量注入
        \App\Process\Router::run();
//         注入定时任务
        EsCrontab::run();
    }

    public static function onRequest(Request $request, Response $response): void
    {
        try{
            // token动态注入
            Di::getInstance()->set(AsaEsConst::DI_REQUEST_OBJ, new \AsaEs\Utility\Request($request));
            // 系统中间件注入
            Dispatch::run($request, $response);
            // 记录访问时间
            $request->withAttribute(AsaEsConst::LOG_ACCESS, microtime(true));
        }catch (\Throwable $throwable){
            throw new MiddlewareException($throwable->getCode(),$throwable->getMessage());
        }
    }

    public static function afterAction(Request $request, Response $response): void
    {
        AccessLog::getInstance()->handle($request,$response);
    }
}
