<?php

namespace AsaEs\Middleware;

use App\Utility\Exception\MiddlewareException;
use AsaEs\Config;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

/**
 * 跨域配置
 */
class CrossDomain
{
    use Singleton;

    public function handle(Request $request, Response $response):void
    {
        // 如果是option请求 则放过
        if ('OPTIONS' == $request->getMethod()) {
            $response->withHeader('Access-Control-Allow-Origin', '*');
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
            $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, token, identity');

            $response->withStatus(Status::CODE_OK);
            $response->end();
        }

        // 判断跨域
        $corsDomain = Config::getInstance()->getConf('auth.CROSS_DOMAIN', true);
        $origin = current($request->getHeader('origin') ?? null) ?? '';
        $origin = rtrim($origin, '/');
        if (ArrayUtility::arrayFlip($corsDomain, $origin)) {
            $response->withHeader('Access-Control-Allow-Origin', $origin);
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
            $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, token, identity');
        }
    }
}
