<?php

namespace AsaEs\Utility;

use AsaEs\AsaEsConst;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Swoole\ServerManager;

class Env
{

    /**
     * 判断程序运行方式是否为HTTP
     */
    public static function isHttp()
    {
        // work_id
        $workId = ServerManager::getInstance()->getServer()->worker_id ?? -1;
        // 唯一请求id
        $requestObj = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ) ?? null;

        if ($workId  < 0 || !is_object($requestObj)) {
            return false;
        }

        return true;
    }
}
