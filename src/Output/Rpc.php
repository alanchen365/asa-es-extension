<?php

namespace AsaEs\Output;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Logger\FileLogger;
use EasySwoole\Config;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\Rpc\Common\ServiceResponse;
use EasySwoole\Core\Swoole\Task\TaskManager;

class Rpc
{
    public static function setBody(ServiceResponse $response, Results $results, int $code = 100000, string $msg = '', bool $successFlg = true): void
    {
        // 返回值封装
        $msg = empty($msg) ? Msg::get($code) : $msg;
        $results->setMsg((string) $msg);

        // 成功逻辑
        if ($successFlg) {
            $results->setCode(empty($code) ? AppInfo::RESULTS_RETURN_SUCCES_CODE : $code);
            $data = $results->getData();
        } else {
            $results->setCode(empty($code) ? 0 : $code);
            $data = $results->getData();
        }

        $response->setResult(json_encode($data));
        return;
    }

    public static function failBody(ServiceResponse $response, Results $results, int $code = 0, string $msg = ''): void
    {
        self::setBody($response, $results, $code, $msg, false);
    }
}
