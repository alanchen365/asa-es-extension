<?php

namespace AsaEs\Utility;

use AsaEs\AsaEsConst;
use AsaEs\Logger\FileLogger;
use AsaEs\Output\Msg;
use EasySwoole\Config;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\Logger;

class ExceptionUtility
{

    /**
     * 精简trace
     */
    public static function simplifyTrace(?array $trace)
    {
        $traceCount = count($trace);
        return array_splice($trace, 0, $traceCount-13);
    }

    /**
     * 获取异常信息
     */
    public static function getExceptionData(\Throwable $throwable, ?int $code = 0, ?string $msg = '')
    {
        $msg = empty($msg)  ? $throwable->getMessage() : $msg;
        $code = empty($code) ?  $throwable->getCode() : $code;

        $headerServer = [];
        $defaultData = [
            'code' => $code,
            'msg' => empty($msg) ? Msg::get($code) : $msg,
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => ExceptionUtility::simplifyTrace($throwable->getTrace()),
            'time' => Time::getNowDataTime()
        ];

        // 是否是http方式运行
        if (Env::isHttp()) {
            $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
            $rawContent= $requestObj->getRawContent();
            $swooleRequest= $requestObj->getSwooleRequest();

            $headerServer1 = array_merge($swooleRequest['header'] ?? [] , $swooleRequest['server'] ??[]);
            $headerServer2 = [
                'fd' => $swooleRequest['fd'] ?? null,
                'request' => $swooleRequest['request'] ?? null,
                'cookie' => $swooleRequest['cookie'] ?? null,
                'get_params' => $swooleRequest['get'] ?? null,
                'post_params' => $swooleRequest['post'] ?? null,
                'json_params' => $rawContent,
                'files_params' => $swooleRequest['files'] ?? null,
                'tmpfiles' => $swooleRequest['tmpfiles'] ?? null,
            ];

            $headerServer = array_merge($headerServer1,$headerServer2);
            $headerServer[AsaEsConst::REQUEST_ID]= $requestObj->getRequestId();
        }

        return array_merge($defaultData,$headerServer) ?? [];
    }
}
