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
use AsaEs\Exception\AppException;
use AsaEs\Exception\MsgException;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use EasySwoole\Core\Http\Message\UploadFile;
use OSS\OssClient;

class OssService extends BaseBaseservice {


    /**
     * 用户信息
     * @param int $userId
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function uploads(UploadFile $file, string $path , bool $isIgnoreErr = false) :array {

        // 参数整理
        $requestParams = [
            'system_id' => AppInfo::SYSTEM_ID,
        ];

        // 获取配置文件
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_RPC);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance(RpcConst::TRACKING_RRC_SERVICE_CONF);
        $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'],'Oss','getOssConfig',$requestParams);

        $ossConfig = $res['oss-config'] ?? null;
        if (!$ossConfig) {
            throw new MsgException(1000, 'Oss文件上传失败');
        }

        $ossClient = new OssClient($ossConfig['access_id'], $ossConfig['access_key'], $ossConfig['endpoint']);

        try {
            // 截取上传原始文件信息
            $path = trim($path, '/');
            $original = $file->getClientFilename();
            $ext = pathinfo($original, PATHINFO_EXTENSION);
            $name = pathinfo($original, PATHINFO_FILENAME);

            $newName = date('YmdHis') . rand(0, 10000) . '.' . $ext;
            $location = "{$path}/{$newName}";
            $filePath = $file->getTempName();

            //上传图片
            $result = $ossClient->uploadFile($ossConfig['bucket'], $location, $filePath);

            if (false === $result) {
                throw new MsgException(1000, 'Oss文件上传失败');
            }

            // 保存上传日志
            $requestParams = [
                'system_id' => AppInfo::SYSTEM_ID,
                'hash' => $result['x-oss-hash-crc64ecma'] ?? '',
                'host' => $result['oss-requestheaders']['Host'] ?? '',
                'original' => $file->getClientFilename(),
                'location' => "/".$location,
                'ext' => $ext,
            ];
            $res = $remoteService->request(RpcConst::TRACKING_RRC_SERVICE_CONF['serviceName'],'Oss','insertUprcord',$requestParams);


            return $res['oss-file'];
        } catch (\Throwable $throwable) {
            throw new AppException($throwable->getCode(),$throwable->getMessage());
        }
    }
}