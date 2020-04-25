<?php

namespace AsaEs\Middleware;

use App\AppConst\AppInfo;
use App\Utility\Exception\MiddlewareException;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Exception\AppException;
use AsaEs\Logger\FileLogger;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasySwoole\Core\Swoole\ServerManager;

/**
 * 跨域配置
 */
class AccessLog
{
    use Singleton;

    public function handle(Request $request, Response $response):void
    {
        try {
            //从请求里获取之前增加的时间戳
            $reqTime = $request->getAttribute(AsaEsConst::LOG_ACCESS);
            //计算一下运行时间
            $runTime = round(microtime(true) - $reqTime, 5);
            //获取用户IP地址
            $ip = ServerManager::getInstance()->getServer()->connection_info($request->getSwooleRequest()->fd);
            $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);

            //拼接一个简单的日志
            $runTime = round(floatval($runTime * 1000), 2);
            $logStr = $ip['remote_ip'] . ' | ' . $runTime . ' ms | ' . $request->getUri() . ' | ' . $requestObj->getRequestId() . ' | ' . $request->getHeader('user-agent')[0];

            //判断一下当执行时间大于1秒记录到 slowlog 文件中，否则记录到 access 文件
            if ($runTime < round(600, 2)) {
                FileLogger::getInstance()->log($logStr, AsaEsConst::LOG_ACCESS);
            } else {
                FileLogger::getInstance()->log($logStr, AsaEsConst::LOG_SLOW);
            }
        } catch (\Throwable $throwable) {
            throw new MiddlewareException($throwable->getCode(), $throwable->getMessage());
        }
    }
}
