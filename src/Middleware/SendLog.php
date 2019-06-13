<?php

namespace AsaEs\Middleware;

use App\AppConst\AppInfo;
use App\Utility\Exception\MiddlewareException;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Logger\FileLogger;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Crontab\CronTab;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

/**
 * 跨域配置
 */
class SendLog
{
    use Singleton;

    public function handle(Request $request, Response $response):void
    {
        try{
            CronTab::getInstance()->addRule('ASAES-SEND-LOG', '00 02 * * *', function () {
                FileLogger::getInstance()->log("写log", "ASAES-SEND-LOG");
            });
        }catch (\Throwable $throwable){
            throw new MiddlewareException($throwable->getCode(),$throwable->getMessage());
        }
    }
}
