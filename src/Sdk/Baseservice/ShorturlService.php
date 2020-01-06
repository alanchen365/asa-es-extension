<?php
/**
 * Created by PhpStorm.
 * User: shixq
 * Date: 2019-06-18
 * Time: 15:34
 */

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class ShorturlService extends BaseBaseservice {


    public static function alias(bool $isIgnoreErr = false, $aliasData = []){

        $aliasParams = [];
        // 参数解析
        foreach ($aliasData as $key => $alias){
            $aliasParams[] = [
                'alias' => $alias['alias'] ?? null, // 非必填 如果不填系统自动生成
                'url' => $alias['url'] ?? null, // 必填
                'ip' => $alias['ip'] ?? null,   //  可为空
                'system_id' => AppInfo::SYSTEM_ID,  // 必填
                'base_url' => $alias['base_url']?? null,    // 基础url 必填
            ];
        }

        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'alias_params' => $aliasParams,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'],'Shorturl',__FUNCTION__,$requestParams);

        return $res ?? [];
    }
}
