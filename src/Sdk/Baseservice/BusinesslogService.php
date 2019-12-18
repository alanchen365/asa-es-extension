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
use AsaEs\Utility\Time;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Component\Di;
use http\Env;

class BusinesslogService extends BaseBaseservice {

    /**
     * 写入远程日志
     * @param int $userId 用户id
     * @param string $userName 用户名
     * @param $trackingNo 第三方单号
     * @param string $trackingKey 模块的key
     * @param string $content 日志内容
     * @param bool|null $isIgnoreErr 是否忽略错误
     */
    public static function setLog(string $trackingKey,array $trackingNo,string $content,int $userId,string $userName,?string $createTime = null,?array $otherData = [],?bool $isIgnoreErr = true, ?string $typeName = ''): void
    {
        if(!$createTime){
            $createTime = Time::getNowDataTime();
        }

        // 参数整理
        $requestParams = [
            'user_id' => $userId,
            'create_username' => $userName,
            'tracking_no' => $trackingNo,
            'tracking_key' => $trackingKey,
            'content' => $content,
            'system_id' => AppInfo::SYSTEM_ID,
            'create_time' => $createTime,
            'type_name' => $typeName,
            'other_data' => $otherData,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::BUSINESSLOG_RRC_SERVICE_CONF);
        $remoteService->request(RpcConst::BUSINESSLOG_RRC_SERVICE_CONF['serviceName'], 'Index', __FUNCTION__, $requestParams);
    }

    /**
     * 批量写入远程日志
     */
    public static function setLogs(array $businesslogData = [],?bool $isIgnoreErr = true): void
    {
        if(Tools::superEmpty($businesslogData)){
            return;
        }

        // 参数整理
        $requestParams = [
            'businesslog_data' => $businesslogData,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::BUSINESSLOG_RRC_SERVICE_CONF);
        $remoteService->request(RpcConst::BUSINESSLOG_RRC_SERVICE_CONF['serviceName'], 'Index', __FUNCTION__, $requestParams);
    }
}
