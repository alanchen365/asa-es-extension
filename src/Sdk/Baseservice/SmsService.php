<?php

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class SmsService extends BaseBaseservice
{


    /**
     * 发送阿里短信
     * @param int $mobile
     * @param string $signName
     * @param string $templateCode
     * @param string $ip
     * @param int $length
     * @param bool $isIgnoreErr
     * @param string $key
     * @return array
     */
    public static function sendVerifyCode(int $mobile, string $signName, string $templateCode, string $ip, int $length = 4, bool $isIgnoreErr = false, string $key = 'sms-ali'): array
    {
        // 参数整理
        $requestParams = [
            'mobile' => $mobile,
            'signName' => $signName,
            'templateCode' => $templateCode,
            'ip' => $ip,
            'length' => $length,
            'key' => $key,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Sms', __FUNCTION__, $requestParams);

        return $res;
    }


//    /**
//     * 发送阿里短信
//     * @param int $mobile
//     * @param string $signName
//     * @param string $templateCode
//     * @param string $ip
//     * @param int $length
//     * @param string $businessType // 业务类型  登录 login 注册 re
//     * @param bool $isIgnoreErr
//     * @return array
//     */
//    public static function sendVerifyCode(int $mobile, string $signName, string $templateCode, string $ip, int $length = 4, bool $isIgnoreErr = false): array
//    {
//        // 参数整理
//        $requestParams = [
//            'mobile' => $mobile,
//            'signName' => $signName,
//            'templateCode' => $templateCode,
//            'ip' => $ip,
//            'length' => $length,
//            'system_id' => AppInfo::SYSTEM_ID,
//        ];
//
//        // 实例化请求类
//        $res = null;
//        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
//        $remoteService->setIsIgnoreErr($isIgnoreErr);
//        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
//        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Sms', __FUNCTION__, $requestParams);
//
//        return $res;
//    }


    public static function sendMessage($mobile, string $msg, string $signName, string $templateCode, string $ip, bool $isIgnoreErr = false): array
    {
        if(!is_array($mobile)){
            $mobile = [$mobile];
        }
        
        // 参数整理
        $requestParams = [
            'mobile' => $mobile,
            'msg' => $msg,
            'signName' => $signName,
            'templateCode' => $templateCode,
            'ip' => $ip,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Sms', __FUNCTION__, $requestParams);

        return $res;
    }


    /**
     * 验证阿里短信
     * @param int $mobile
     * @param string $msg
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function isVerifyCode(int $mobile, string $msg, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'mobile' => $mobile,
            'msg' => $msg,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Sms', __FUNCTION__, $requestParams);

        return $res;
    }
}