<?php

namespace AsaEs\Logger;

use AsaEs\AsaEsConst;
use AsaEs\Utility\Env;
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

    /**
     * 获取请求中和用户相关的日志
     */
    public static function getRequestLogData() :array {

        $headerServer = [];

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

        return $headerServer;
    }
}
