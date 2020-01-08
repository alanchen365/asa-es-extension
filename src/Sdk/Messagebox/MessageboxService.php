<?php

namespace AsaEs\Sdk\Baseservice\Messagebox;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\Exception\MsgException;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;

class MessageboxService
{
    /**
     * 群发消息
     * @param bool $isIgnoreErr
     * @param array $messageboxParams
     * @return array
     */
    public static function send(bool $isIgnoreErr = false,array $messageboxParams = []): array
    {
        // 参数整理
        $requestParams = [
            'messagebox_params' => $messageboxParams,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Messagebox', __FUNCTION__, $requestParams);

        return $res;
    }

    public static function list(bool $isIgnoreErr = false,int $messageboxId,string $userId,int $pageNo,int $pageNum,string $orderBy = 'desc'){

        // 参数整理
        $requestParams = [
            'id' => $messageboxId,
            'from_user_id' => $userId,
            'page_no' => $pageNo,
            'page_num' => $pageNum,
            'order_by' => $orderBy,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Messagebox', __FUNCTION__, $requestParams);

        return $res;
    }

    public static function read(bool $isIgnoreErr = false,int $messageboxId,string $userId){

        // 参数整理
        $requestParams = [
            'id' => $messageboxId,
            'from_user_id' => $userId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Messagebox', __FUNCTION__, $requestParams);

        return $res;
    }

    public static function delete(bool $isIgnoreErr = false,int $messageboxId,string $userId){

        // 参数整理
        $requestParams = [
            'id' => $messageboxId,
            'from_user_id' => $userId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'], 'Messagebox', __FUNCTION__, $requestParams);

        return $res;
    }
}
