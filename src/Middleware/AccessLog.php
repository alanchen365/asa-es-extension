<?php

namespace AsaEs\Middleware;

use App\AppConst\AppInfo;
use App\Utility\Exception\MiddlewareException;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Exception\AppException;
use AsaEs\Logger\BaseLogger;
use AsaEs\Logger\FileLogger;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;
use EasySwoole\Core\Swoole\ServerManager;
use Es3\Utility\File;

/**
 * 跨域配置
 */
class AccessLog
{
    use Singleton;

    public function handle(Request $request, Response $response): void
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
            $runTime = round(floatval($runTime * 1000), 0);
            $accessLog = [
                AppInfo::APP_EN_NAME,
                AppInfo::APP_NAME,
                $requestObj->getRequestId(),
                $request->getMethod(),
                $request->getUri(),
                $ip['remote_ip'] ?? 'unknown',
                "{$runTime} ms",
                $request->getHeader('user-agent')[0] ?? '',
                json_encode(BaseLogger::getRequestLogData()),
                json_encode(debug_backtrace()),
            ];

            /** 正常日志 */
            FileLogger::getInstance()->log($accessLog, AsaEsConst::LOG_ACCESS);

            $accessLog = implode($accessLog, '  |   ');
            if ($runTime > round(1 * 1000, 0)) {
                $logPath = \EasySwoole\Config::getInstance()->getConf('LOG_DIR') . "/" . AsaEsConst::LOG_SLOW;
                $fileDate = date('Ymd', time());
                $filePath = "{$logPath}/{$fileDate}.log";

                clearstatcache();
                is_dir($logPath) ? null : File::createDirectory($logPath, 0777);
                file_put_contents($filePath, "{$accessLog}", FILE_APPEND | LOCK_EX);
            }
        } catch (\Throwable $throwable) {
            throw new MiddlewareException($throwable->getCode(), $throwable->getMessage());
        }
    }
}
