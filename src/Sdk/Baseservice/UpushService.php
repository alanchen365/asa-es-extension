<?php

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class UpushService extends BaseBaseservice
{

    /**
     * 发送友盟推送 广播
     * @param string $key
     * @param string $displayType
     * @param string $ticker
     * @param string $title
     * @param string $text
     * @param string $iosAlert
     * @param string $custom
     * @param string $afterOpen
     * @param int $pushId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function send(string $key, string $displayType, string $ticker, string $title, string $text, string $iosAlert, string $custom, ?string $afterOpen = 'go_app', ?string $pushId, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'key' => $key,
            'display_type' => $displayType,
            'ticker' => $ticker,
            'title' => $title,
            'text' => $text,
            'ios_alert' => $iosAlert,
            'custom' => $custom,
            'after_open' => $afterOpen,
            'push_id' => $pushId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Upush', __FUNCTION__, $requestParams);

        return $res['upush'] ?? [];
    }

    /**
     * 发送友盟推送 组播
     * @param string $key
     * @param string $displayType
     * @param string $ticker
     * @param string $title
     * @param string $text
     * @param string $iosAlert
     * @param string $notLaunchFrom
     * @param string $launchFrom
     * @param string $custom
     * @param string $afterOpen
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function sendGroup(string $key, string $displayType, string $ticker, string $title, string $text, string $iosAlert, ?string $notLaunchFrom, ?string $launchFrom, ?string $custom, ?string $afterOpen = 'go_app', bool $isIgnoreErr = false): array
    {
        // 一段时间内不活跃
        if (!empty($notLaunchFrom)) {
            $filter = array(
                "where" => array(
                    "and" => array(
                        array(
                            "not_launch_from" => $notLaunchFrom
                        )
                    )
                )
            );
        }

        // 一段时间内活跃
        if (!empty($launchFrom)) {
            $filter = array(
                "where" => array(
                    "and" => array(
                        array(
                            "launch_from" => $launchFrom
                        )
                    )
                )
            );
        }

        if (empty($filter)) {
            return false;
        }

        // 参数整理
        $requestParams = [
            'key' => $key,
            'display_type' => $displayType,
            'ticker' => $ticker,
            'title' => $title,
            'text' => $text,
            'ios_alert' => $iosAlert,
            'filter' => $filter,
            'custom' => $custom,
            'after_open' => $afterOpen,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Upush', __FUNCTION__, $requestParams);

        return $res['upush'] ?? [];
    }

    /**
     * 发送友盟推送 Android单播
     * @param string $key
     * @param string $displayType
     * @param string $ticker
     * @param string $title
     * @param string $text
     * @param string $deviceTokens
     * @param string $custom
     * @param string $afterOpen
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function sendAndroid(string $key, string $displayType, string $ticker, string $title, string $text, string $deviceTokens, ?string $custom, ?string $afterOpen = 'go_app', bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'key' => $key,
            'display_type' => $displayType,
            'ticker' => $ticker,
            'title' => $title,
            'text' => $text,
            'device_tokens' => $deviceTokens,
            'custom' => $custom,
            'after_open' => $afterOpen,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Upush', __FUNCTION__, $requestParams);

        return $res['upush'] ?? [];
    }

    /**
     * 发送友盟推送 IOS单播
     * @param string $key
     * @param string $iosAlert
     * @param string $iosDeviceTokens
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function sendIOS(string $key, string $iosAlert, string $iosDeviceTokens, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'key' => $key,
            'ios_alert' => $iosAlert,
            'ios_device_tokens' => $iosDeviceTokens,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Upush', __FUNCTION__, $requestParams);

        return $res['upush'] ?? [];
    }
}
