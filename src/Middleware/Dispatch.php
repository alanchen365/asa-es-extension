<?php

namespace AsaEs\Middleware;

use AsaEs\Config;

/**
 * 中间件调度
 * Class Dispatch
 * @package AsaEs\Middleware
 */
class Dispatch
{

    /**
     * 注入
     */
    public static function run($request,$response)
    {
        $middlewareConf = Config::getInstance()->getConf('middleware');
        if(!isset($middlewareConf['EmptyParamFilter']) || $middlewareConf['EmptyParamFilter'] !== false){
            // 空参数过滤
            EmptyParamFilter::getInstance()->handle($request, $response);
        }

        if(!isset($middlewareConf['CrossDomain']) || $middlewareConf['CrossDomain'] !== false){
            // 跨域注入
            CrossDomain::getInstance()->handle($request, $response);
        }

        if(!isset($middlewareConf['AutoToken']) || $middlewareConf['AutoToken'] !== false){
            // 本机、开发环境 自动携带token  测试环境 swagger访问 自动携带token
            AutoToken::getInstance()->handle($request, $response);
        }
    }
}
