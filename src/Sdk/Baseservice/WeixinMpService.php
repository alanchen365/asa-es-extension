<?php
/**
 * Created by PhpStorm.
 * User: Lius
 * Date: 2019/7/8
 * Time: 11:42
 */

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;

class WeixinMpService extends BaseBaseservice
{

    /**
     * 通过微信code值获取用户信息
     * @param string $code
     * @param bool $isIgnoreErr
     * @return array
     */
    public function getUserInfoByCode(string $code, bool $isIgnoreErr = false): array{

        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'code' => $code,
        ];

        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF['serviceName'], 'WeixinLogin', __FUNCTION__, $requestParams);

        return $res['userInfo'] ?? [];
    }

    /**
     * 通过code获取AccessToken和OpenId
     * @param string $code
     * @param bool $isIgnoreErr
     * @return array
     */
    public function getOpenIdByCode(string $code, bool $isIgnoreErr = false): array{

        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'code' => $code,
        ];

        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF['serviceName'], 'WeixinLogin', __FUNCTION__, $requestParams);

        return $res['resultObj'] ?? [];
    }

    /**
     * 通过OpenId获取用户信息
     * @param string $code
     * @param string $accessToken
     * @param bool $isIgnoreErr
     * @return array
     */
    public function getUserInfoByOpenId(string $code, string $accessToken, bool $isIgnoreErr = false): array{

        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'code' => $code,
            'access_token' => $accessToken,
        ];

        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF['serviceName'], 'WeixinLogin', __FUNCTION__, $requestParams);

        return $res['userInfoObj'] ?? [];
    }

    /**
     * 推送模板消息
     * @param string $toUser
     * @param string $templateId
     * @param array $data
     * @param string $url
     * @param array $miniprogram
     * @param bool $isIgnoreErr
     * @return int
     */
    public function sendTemplate(string $toUser, string $templateId, array $data, string $url = '', array $miniprogram = [], bool $isIgnoreErr = false): int{

        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'toUser' => $toUser,
            'templateId' => $templateId,
            'data' => $data,
            'url' => $url,
            'miniprogram' => $miniprogram,
        ];
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF['serviceName'], 'WeixinMessage', __FUNCTION__, $requestParams);

        return $res['msgId'] ?? 0;

    }


    public function getSignature(string $url, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
            'url' => $url,
        ];

        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::WEIXIN_MP_RRC_SERVICE_CONF['serviceName'], 'WeixinLogin', __FUNCTION__, $requestParams);

        return $res['sign_obj'] ?? [];
    }

}