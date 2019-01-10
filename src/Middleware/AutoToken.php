<?php

namespace AsaEs\Middleware;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Exception\Service\MiddlewareException;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

/**
 * 跨域配置
 */
class AutoToken
{
    use Singleton;

    public function handle(Request $request, Response $response):void
    {
        // 获取配置
        $env = Config::getInstance()->getConf('ENV');
        $tokenStr = Config::getInstance()->getConf('auth.TOKEN', true);
        $swaggerDomain = Config::getInstance()->getConf('auth.SWAGGER_DOMAIN', true);
        $origin = current($request->getHeader('origin') ?? null) ?? '';
        $origin = rtrim($origin, '/');

        // 如果是本机 及 开发环境 及swagger 模拟一个用户出来
        if (AppInfo::APP_TOKEN_AUTH_SWITCH) {
            $esRequest = Di::getInstance()->get(AsaEsConst::DI_REQUEST_OBJ);
            $esTokenStr = $esRequest->getHeaderToken();

            // 本机和开发环境的模拟
            if (ArrayUtility::arrayFlip(['LOCAL','DEVELOP'], $env) && !$esTokenStr) {
                $esRequest->setHeaderToken($tokenStr);
            }
            // test swagger 模拟用户
            if ($swaggerDomain  &&  $origin  && $env == "TESTING" && $swaggerDomain == $origin) {
                $esRequest->setHeaderToken($tokenStr);
            }
        }
    }
}
