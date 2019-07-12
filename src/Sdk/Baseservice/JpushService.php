<?php

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class JpushService extends BaseBaseservice
{

    /**
     * 发送极光推送
     * @param string $key
     * @param string $title
     * @param string $platform
     * @param string $type
     * @param string $pushId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function send(string $key, string $title, ?string $platform = 'all', ?string $type = '0', ?string $pushId = '0', bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'key' => $key,
            'title' => $title,
            'platform' => $platform,
            'type' => $type,
            'push_id' => $pushId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Jpush', __FUNCTION__, $requestParams);

        return $res;
    }

}