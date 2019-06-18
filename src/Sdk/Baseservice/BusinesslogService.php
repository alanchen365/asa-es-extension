<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/2/26
 * Time: 10:58
 */

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use App\Module\Auth\Service\UserService;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Exception\MsgException;
use AsaEs\RemoteCall\Curl;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Blake;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Component\Di;
use http\Env;

class BusinesslogService {


    /**
     * 写入远程日志
     * @param $trackingNo 第三方单号
     * @param string $trackingKey 模块的key
     * @param string $content 日志内容
     * @param bool|null $isIgnoreErr 是否忽略错误
     * @param bool $isRpc 是否使用rpc方式调用
     * @throws MsgException
     */
    public static function set($trackingNo,string $trackingKey,string $content,?bool $isIgnoreErr = true){

        // 确定调用方式
        $isRpc = RpcConst::BUSINESSLOG_RRC_SERVICE_CONF['enable'] ?? false;
        $requestWay = $isRpc ? RemoteService::REQUEST_WAY_RPC : RemoteService::REQUEST_WAY_CURL;

        // 参数整理
        $userId = 1;
        $userName = 'test';

        // 如果单号不是数组 强制转一下
        if(!is_array($trackingNo)){
            $trackingNo = [$trackingNo];
        }

        $requestParams = [
            'system_id' => blake2(AppInfo::APP_EN_NAME, EnvConst::BLAKE2_LENGTH, EnvConst::BLAKE2_KEY),
            'user_id' => $userId,
            'create_username' => $userName,
            'tracking_no' => $trackingNo,
            'tracking_key' => $trackingKey,
            'content' => $content,
        ];
        
        // 实例化请求类
        $remoteService = new RemoteService($requestWay);
        $remoteService->setIsIgnoreErr($isIgnoreErr);

        $res = null;
        if($requestWay == RemoteService::REQUEST_WAY_CURL){
            $remoteService->getInstance([],true, $isIgnoreErr);
            $res = $remoteService->request("POST", EnvConst::BASESERVICE_ADDRESS.":20500/web/businesslog/businesslog", $requestParams,$isIgnoreErr);

        }elseif (RemoteService::REQUEST_WAY_RPC){

            $remoteService->getInstance(RpcConst::BUSINESSLOG_RRC_SERVICE_CONF);
            $res = $remoteService->request(RpcConst::BUSINESSLOG_RRC_SERVICE_CONF['serviceName'],'Index','setLog',$requestParams);
        }

    }
}