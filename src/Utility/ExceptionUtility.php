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
        return array_splice($trace, 0, $traceCount-12);
    }

    /**
     * 获取异常信息
     */
    public static function getExceptionData(\Throwable $throwable, ?int $code = 0, ?string $msg = '')
    {
        // request id
        $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);

        $msg = empty($msg)  ? $throwable->getMessage() : $msg;
        $code = empty($code) ?  $throwable->getCode() : $code;

        $data = [
            'code' =>$code,
            'msg' => empty($msg) ? Msg::get($code) : $msg,
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => ExceptionUtility::simplifyTrace($throwable->getTrace()),
            'swoole_request' => $requestObj->getSwooleRequest(),
            'raw_content' =>$requestObj->getRawContent(),
            AsaEsConst::REQUEST_ID=>$requestObj->getRequestId(),
        ];

        return $data ?? [];
    }
}
