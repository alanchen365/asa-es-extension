<?php

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class MobileService extends BaseBaseservice
{

    /**
     * 获取手机号
     * @param string $accessToken
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function getMobile(string $accessToken, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'access_token' => $accessToken,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Mobile', __FUNCTION__, $requestParams);

        return $res['mobile_obj'];
    }
}