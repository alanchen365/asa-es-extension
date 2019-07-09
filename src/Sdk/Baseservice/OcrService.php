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

class OcrService extends BaseBaseservice {


    /**
     * 识别营业执照
     * @param int $userId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function discernBusinessLicense(string $image,bool $isIgnoreErr = false) :array {

        // 参数整理
        $requestParams = [
            'image' => $image,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'],'Ocr',__FUNCTION__,$requestParams);

        return $res['businesslicense_obj'] ?? [];
    }

    /**
     * 识别身份证
     * @param int $userId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function discernIdcard(string $image,string $side,bool $isIgnoreErr = false) :array {

        // 参数整理
        $requestParams = [
            'image' => $image,
            'side' => $side,
            'system_id' => AppInfo::SYSTEM_ID,
        ];
        
        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'],'Ocr',__FUNCTION__,$requestParams);

        return $res['idcard_obj'] ?? [];
    }

    public static function discernOpenAccount(string $image,int $imageType = 1,bool $isIgnoreErr = false) :array {

        // 参数整理
        $requestParams = [
            'image' => $image,
            'img_type' => $imageType,
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'],'Ocr',__FUNCTION__,$requestParams);

        return $res['open_account_obj'] ?? [];
    }
}