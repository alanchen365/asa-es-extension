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

    /**
     * 随机生成短网址
     * @param bool $isIgnoreErr
     * @param string $customize
     * @param string $url
     * @param string|null $baseUrl
     * @param string|null $ip
     * @return array
     */
    public static function rand(bool $isIgnoreErr = false,string $url,string $baseUrl, string $ip = null):array
    {
        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'url' => $url,
            'base_url' => $baseUrl,
            'ip' => $ip,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'],'Shorturl',__FUNCTION__,$requestParams);

        return $res ?? [];
    }


    public static function customize(bool $isIgnoreErr = false, string $customize,string $url,string $baseUrl, string $ip = null):array
    {
        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'customize' => $customize,
            'url' => $url,
            'base_url' => $baseUrl,
            'ip' => $ip,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'],'Shorturl',__FUNCTION__,$requestParams);

        return $res ?? [];
    }

    public static function unCustomize(bool $isIgnoreErr = false, string $customize):array
    {
        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'customize' => $customize,
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
