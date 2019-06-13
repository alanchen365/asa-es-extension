<?php

namespace AsaEs\Middleware;

use App\Utility\Exception\MiddlewareException;
use AsaEs\Utility\Tools;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

/**
 * 空参数过滤
 */
class EmptyParamFilter
{
    use Singleton;

    public function handle(Request $request, Response $response):void
    {
        if($request->getMethod() != 'GET'){
            return ;
        }

        $params = $request->getQueryParams() ?? [];
        foreach ($params as $key => $val){
            if(Tools::superEmpty($val)){
                unset($params[$key]);
            }
        }

        $request->withQueryParams($params);
    }
}
