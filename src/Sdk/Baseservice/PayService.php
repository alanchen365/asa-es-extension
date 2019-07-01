<?php

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class PayService extends BaseBaseservice
{

    /**
     * 支付信息
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function index(bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::PAY_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::PAY_RRC_SERVICE_CONF['serviceName'], 'Pay', __FUNCTION__, $requestParams);

        return $res;
    }
    
    /**
     * 支付宝支付
     * @param string $orderNo
     * @param float $amount
     * @param string $key 渠道id
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function aLiPay(string $orderNo, float $amount, string $key, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'order_no' => $orderNo,
            'amount' => $amount,
            'key' => $key,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::PAY_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::PAY_RRC_SERVICE_CONF['serviceName'], 'Pay', __FUNCTION__, $requestParams);

        return $res;
    }

    /**
     * 支付宝打款
     * @param string $orderNo
     * @param float $amount
     * @param string $key 渠道id
     * @param string $alipayAccount
     * @param string $siteName
     * @param string $realName
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function aLiPayExpend(string $orderNo, float $amount, string $key, string $alipayAccount, string $siteName, string $realName, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'order_no' => $orderNo,
            'amount' => $amount,
            'key' => $key,
            'alipay_account' => $alipayAccount,
            'site_name' => $siteName,
            'real_name' => $realName,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::PAY_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::PAY_RRC_SERVICE_CONF['serviceName'], 'Pay', __FUNCTION__, $requestParams);

        return $res;
    }

    /**
     * 微信支付
     * @param string $orderNo
     * @param float $amount
     * @param string $key 渠道id
     * @param string $spbillCreateIp
     * @param string $body
     * @param string $type
     * @param string $wapUrl
     * @param string $wapName
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function wxPay(string $orderNo, float $amount, string $key, string $spbillCreateIp, string $body, string $type, string $wapUrl, string $wapName, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'order_no' => $orderNo,
            'amount' => $amount,
            'key' => $key,
            'spbill_create_ip' => $spbillCreateIp,
            'body' => $body,
            'type' => $type,
            'wap_url' => $wapUrl,
            'wap_name' => $wapName,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::PAY_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::PAY_RRC_SERVICE_CONF['serviceName'], 'Pay', __FUNCTION__, $requestParams);

        return $res;
    }

    /**
     * 微信打款
     * @param string $orderNo
     * @param float $amount
     * @param string $key 渠道id
     * @param string $openId
     * @param string $spbillCreateIp
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function wxPayExpend(string $orderNo, float $amount, string $key, string $openId, string $spbillCreateIp, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'order_no' => $orderNo,
            'amount' => $amount,
            'key' => $key,
            'open_id' => $openId,
            'spbill_create_ip' => $spbillCreateIp,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::PAY_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::PAY_RRC_SERVICE_CONF['serviceName'], 'Pay', __FUNCTION__, $requestParams);

        return $res;
    }
}