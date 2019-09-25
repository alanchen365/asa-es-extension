<?php

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class ConfigService extends BaseBaseservice
{

    /**
     * 设置配置
     * @param int $key
     * @param string $value
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function setConfig(string $key, string $value, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'key' => $key,
            'value' => $value,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TAOKE_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TAOKE_RRC_SERVICE_CONF['serviceName'], 'Config', __FUNCTION__, $requestParams);

        return $res;
    }
}