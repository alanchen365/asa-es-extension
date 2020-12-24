<?php

namespace AsaEs\Middleware;

use App\Utility\Exception\MiddlewareException;
use AsaEs\Config;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

/**
 * 跨域配置
 */
class CrossDomain
{
    use Singleton;

    public function handle(Request $request, Response $response): void
    {
        $env = $confVal = Config::getInstance()->getEnv();
        $flg = false;   // 是否允许跨域
        $origin = null; // 跨域的域名

        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, app_code, system_id, token, identity, app_code');

        // 如果是option请求 直接返回
        if ('OPTIONS' == $request->getMethod()) {
            $response->withStatus(Status::CODE_OK);
            $response->end();
        }

        // 如果是生产环境，给指定域名做跨域
        if ($env == 'PRODUCTION') {

            $corsDomain = Config::getInstance()->getConf('auth.CROSS_DOMAIN', true);
            $origin = current($request->getHeader('origin') ?? null) ?? '';
            $origin = rtrim($origin, '/');
            if (ArrayUtility::arrayFlip($corsDomain, $origin)) {
                $flg = true;
            }
        } else {
            $origin = '*';
            $flg = true;
        }

        //  允许跨域
        if ($flg) {
            $response->withHeader('Access-Control-Allow-Origin', $origin);
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }
    }
}
