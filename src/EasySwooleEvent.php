<?php

namespace AsaEs;

use App\AppConst\AppInfo;
use AsaEs\Config\Router;
use AsaEs\Exception\Service\SignException;
use AsaEs\Exception\SystemException;
use AsaEs\Logger\AccessLogger;
use AsaEs\Logger\FileLogger;
use AsaEs\Process\Inotify;
use AsaEs\Router\HttpRouter;
use AsaEs\Utility\ArrayUtility;
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
        HttpRouter::getInstance()->registered();
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
    }

    public static function onRequest(Request $request, Response $response): void
    {
        // 记录访问时间
        $request->withAttribute('requestTime', microtime(true));
        Di::getInstance()->set(AsaEsConst::DI_REQUEST_OBJ, new \AsaEs\Utility\Request($request));
        $requestHost = current($request->getHeader('host') ?? null) ?? '';
        
        // 获取配置
        $env = Config::getInstance()->getConf('ENV');
        $corsDomain = Config::getInstance()->getConf('auth.CROSS_DOMAIN', true);
        $whitelistsRoute = Config::getInstance()->getConf('auth.NO_AUTH_ROUTE', true);
        $tokenStr = Config::getInstance()->getConf('auth.TOKEN', true);
        $swaggerDomain = Config::getInstance()->getConf('auth.SWAGGER_DOMAIN',true);

        // 如果是option请求 则放过
        if ('OPTIONS' == $request->getMethod()) {
            $response->withHeader('Access-Control-Allow-Origin', '*');
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
            $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

            $response->withStatus(Status::CODE_OK);
            $response->end();
        }

        // 判断跨域
        $origin = current($request->getHeader('origin') ?? null) ?? '';
        $origin = rtrim($origin, '/');
        if (ArrayUtility::arrayFlip($corsDomain, $origin)) {
            $response->withHeader('Access-Control-Allow-Origin', $origin);
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
            $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        }

        // 如果是本机 及 开发环境 及swagger 模拟一个用户出来
        if (AppInfo::APP_TOKEN_AUTH_SWITCH) {
            $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
            $esTokenStr = $esRequest->getHeaderToken();

            // 本机和开发环境的模拟
            if (ArrayUtility::arrayFlip(['LOCAL','DEVELOP'], $env ) && !$esTokenStr) {
                $esRequest->setHeaderToken($tokenStr);
            }

            // test swagger 模拟用户
            if ($env == "TESTING" && $swaggerDomain == $origin) {
                $esRequest->setHeaderToken($tokenStr);
            }
        }
    }

    public static function afterAction(Request $request, Response $response): void
    {
        //从请求里获取之前增加的时间戳
        $reqTime = $request->getAttribute('requestTime');
        //计算一下运行时间
        $runTime = round(microtime(true) - $reqTime, 3);
        //获取用户IP地址
        $ip = ServerManager::getInstance()->getServer()->connection_info($request->getSwooleRequest()->fd);

        //拼接一个简单的日志
        $logStr = ' | '.$ip['remote_ip'] .' | '. $runTime . '|' . $request->getUri() .' | '.
            $request->getHeader('user-agent')[0];
        //判断一下当执行时间大于1秒记录到 slowlog 文件中，否则记录到 access 文件
        if ($runTime > 1) {
            Logger::getInstance()->log($logStr, 'slowlog');
        } else {
            logger::getInstance()->log($logStr, 'access');
        }
    }
}
