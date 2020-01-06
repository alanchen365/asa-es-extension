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

class AreaService extends BaseBaseservice {


    /**
     * 用户信息
     * @param int $userId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function getAreaByPathId(string $pathId,bool $isIgnoreErr = false) :array {

        // 参数整理
        $requestParams = [
            'pathid' => $pathId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::AREA_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::AREA_RRC_SERVICE_CONF['serviceName'],'Area',__FUNCTION__,$requestParams);

        return $res['area_names'] ?? [];
    }

    /**
     * 根据地区名称获取地区信息
     * @param int $userId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function getAreaByAreaName(string $areaName, int $parentId = 0, bool $isIgnoreErr = false): array
    {
        // 参数整理
        $requestParams = [
            'area_name' => $areaName,
            'parent_id' => $parentId,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::AREA_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::AREA_RRC_SERVICE_CONF['serviceName'], 'Area', __FUNCTION__, $requestParams);

        return $res['area_list'] ?? [];
    }
}
