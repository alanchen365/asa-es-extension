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

class RbacService extends BaseBaseservice {

    // 登录http地址
    const SET_LOGIN_URL = '/api/rbac/login';
    // 添加门店和供应商地址
    const SET_USER_URL = '/api/rbac/user';

    public static function login(string $account, string $password, ?bool $isIgnoreErr = true)
    {
        // 确定调用方式
        $isRpc = RpcConst::RBAC_RRC_SERVICE_CONF['enable'] ?? false;
        $requestWay = $isRpc ? RemoteService::REQUEST_WAY_RPC : RemoteService::REQUEST_WAY_CURL;

        // 参数整理
        $requestParams = [
            'account' => $account,
            'password' => $password,
            'system_id' => blake2(AppInfo::APP_EN_NAME, EnvConst::BLAKE2_LENGTH, EnvConst::BLAKE2_KEY),
        ];

        // 实例化请求类
        $remoteService = new RemoteService($requestWay);
        $remoteService->setIsIgnoreErr($isIgnoreErr);

        $res = null;
        if ($requestWay == RemoteService::REQUEST_WAY_CURL) {
            $remoteService->getInstance([],true, $isIgnoreErr);
            $res = $remoteService->request("POST", RbacService::getBaseserviceUrl(RbacService::SET_LOGIN_URL), ['body'=>json_encode($requestParams)], $isIgnoreErr);
        } elseif (RemoteService::REQUEST_WAY_RPC) {
            $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
            $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Index','login',$requestParams);
        }
        return $res;
    }

    public static function addUser(array $params, ?bool $isIgnoreErr = true)
    {
        // 确定调用方式
        $isRpc = RpcConst::RBAC_RRC_SERVICE_CONF['enable'] ?? false;
        $requestWay = $isRpc ? RemoteService::REQUEST_WAY_RPC : RemoteService::REQUEST_WAY_CURL;

        // 参数整理
//        $requestParams = [
//            'account' => $account,
//            'password' => $password,
//            'system_id' => blake2(AppInfo::APP_EN_NAME, EnvConst::BLAKE2_LENGTH, EnvConst::BLAKE2_KEY)
//        ];
        $params['system_id'] = blake2(AppInfo::APP_EN_NAME, EnvConst::BLAKE2_LENGTH, EnvConst::BLAKE2_KEY);

        // 实例化请求类
        $remoteService = new RemoteService($requestWay);
        $remoteService->setIsIgnoreErr($isIgnoreErr);

        $res = null;
        if ($requestWay == RemoteService::REQUEST_WAY_CURL) {
            $remoteService->getInstance([],true, $isIgnoreErr);
            $res = $remoteService->request("POST", RbacService::getBaseserviceUrl(RbacService::SET_USER_URL), ['body'=>json_encode($params)], $isIgnoreErr);
        } elseif (RemoteService::REQUEST_WAY_RPC) {
            $remoteService->getInstance(RpcConst::RBAC_RRC_SERVICE_CONF);
            $res = $remoteService->request(RpcConst::RBAC_RRC_SERVICE_CONF['serviceName'],'Index','addUser',$params);
        }
        return $res;
    }
}