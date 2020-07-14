<?php
/**
 * Created by PhpStorm.
 * User: huxinpei
 * Date: 2020/5/12
 * Time: 9:07 AM
 */

namespace AsaEs\Sdk\Baseservice;

use App\AppConst\AppInfo;
use App\AppConst\EnvConst;
use App\AppConst\RpcConst;
use AsaEs\RemoteCall\RemoteService;
use AsaEs\Utility\ArrayUtility;
use AsaEs\Utility\Tools;
use AsaEs\Exception\AppException;
use AsaEs\Exception\MsgException;

class BasicsService extends BaseBaseservice
{

    /**
     * 根据ID获取基础数据 考虑数据量使用POST
     * @param string $dataName 数据名称 供应商：supplier, 产品：product，产品规格：productunit，仓库：depot，货主：platform
     * @param array $ids eg:[1,2,3,4]
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function getDataByIds(string $dataName, array $ids, bool $isIgnoreErr = false): array
    {
        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_CURL);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance();
        $url = BaseBaseservice::getBaseserviceUrl('/pc/basics/' . $dataName);
        $params = ['body' => json_encode(['id' => array_unique($ids)])];
        $res = $remoteService->request('POST', $url, $params);
        return $res ? $res[$dataName . '_list'] : [];
    }

    /**
     * 根据ID获取某字段
     * @param string $dataName 数据名称 供应商：supplier, 产品：product，产品规格：productunit，仓库：depot，货主：platform
     * @param string $field 字段名：eg:name
     * @param array $ids eg:[1,2,3,4]
     * @param bool $isIgnoreErr
     * @return array eg:['id'=>'name','id2'=>'name2']
     */
    public static function getDataFieldByIds(string $dataName, string $field, array $ids, bool $isIgnoreErr = false): array
    {
        $res = self::getDataByIds($dataName, $ids, $isIgnoreErr);

        if (empty($res)) {
            return [];
        }
        return array_column($res, $field, 'id');
    }

    /**
     * 根据数组中ID拼接对应数据
     * @param array $datas 支持二维数组
     * @param array $config
     *      data_name-数据名称，field_map-字段映射 key为查询字段；value为字段别名
     *      eg:[
     *              'owner_level_2_number'=>['data_name'=>'platform','field_map'=>['name'=>'owner_level_2_name','short_name'=>'owner_level_2_short_name']],
     *              'spu_number'=>['data_name'=>'product','field_map'=>['name'=>'spu_name']],
     *              'sku_number'=>['data_name'=>'productunit','field_map'=>['guige'=>'spu_guige']],
     *              'depot_region_id'=>['data_name'=>'depot','field_map'=>['name'=>'depot_region_name']],
     *         ]
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function mergeArrayData(array $datas, array $config, bool $isIgnoreErr = true): array
    {
        try {
            foreach ($config as $configKey => $configValue) {
                //判断是一维数组
                if (count($datas) == count($datas, 1)) {
                    //拉取数据
                    $mapData = self::getDataByIds($configValue['data_name'], [$datas[$configKey]], $isIgnoreErr);
                    foreach ($configValue['field_map'] as $mapKey => $mapValue) {
                        $datas[$mapValue] = $mapData[0][$mapKey] ?? null;
                    }
                } else {
                    //拉取数据
                    $mapData = self::getDataByIds($configValue['data_name'], array_column($datas, $configKey), $isIgnoreErr);
                    $mapData = array_column($mapData, null, 'id');
                    foreach ($configValue['field_map'] as $mapKey => $mapValue) {
                        foreach ($datas as $dataKey => $dataValue) {
                            $datas[$dataKey][$mapValue] = $mapData[$dataValue[$configKey]][$mapKey] ?? null;
                        }
                    }
                }
            }

            return $datas;

        } catch (\Throwable $throwable) {
            throw new AppException($throwable->getCode(), $throwable->getMessage());
        }
    }


    /**
     * 根据数组字段拼接对应产品数据 只支持产品
     * @param array $datas 二维数组
     * @param array $config
     *      key为数据源名称，search_field-搜索字段，field_map-字段映射[value为字段别名]；
     *      eg:[
     *              'product'=>['search_field'=>['platform_id'=>'platform_id','third_pno'=>'spu_number'],'field_map'=>['name'=>'owner_level_2_name','short_name'=>'owner_level_2_short_name']],
     *         ]
     * @param bool $isIgnoreErr
     * @return array
     */

    public static function mergeProductData(array $datas, array $config, bool $isIgnoreErr = true): array
    {
        try {
            foreach ($config as $configKey => $configValue) {
                //拉取数据
                $mapData = self::getDataByFileds($configKey, $datas, $configValue['search_field'], $isIgnoreErr);
                if (empty($mapData)) {
                    continue;
                }
                $newMapData = []; //重新整理BASE数据
                foreach ($mapData as $value) {
                    $keyArr = [];
                    foreach ($configValue['search_field'] as $key => $field) {
                        if (!isset($value[$key])) {
                            continue;
                        }
                        $keyArr[] = $value[$key];
                    }
                    $KeyString = implode('__', $keyArr);
                    $newMapData[$KeyString] = $value;
                }

                foreach ($datas as $dataKey => $dataValue) {
                    $keyArr = [];
                    foreach ($configValue['search_field'] as $key => $field) {
                        if (!isset($dataValue[$field])) {
                            continue;
                        }
                        $keyArr[] = $dataValue[$field];
                    }
                    $KeyString = implode('__', $keyArr);
                    if (isset($newMapData[$KeyString])) {
                        $tmpArr = $newMapData[$KeyString];
                    } else {
                        $tmpArr = [];
                    }

                    foreach ($configValue['field_map'] as $mapKey => $mapValue) {
                        $dataValue[$mapValue] = $tmpArr[$mapKey] ?? null;
                    }
                    $datas[$dataKey] = $dataValue;
                }

            }
            return $datas;
        } catch (\Throwable $throwable) {
            throw new AppException($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * 根据ID获取基础数据 考虑数据量使用POST
     * @param string $dataName 数据名称 供应商：supplier, 产品：product，产品规格：productunit，仓库：depot，货主：platform
     * @param array $datas eg:[1,2,3,4]
     * @param bool $isIgnoreErr
     * @return array
     */
    public static function getDataByFileds(string $dataName, array $datas, array $searchFields, bool $isIgnoreErr = false): array
    {
        //过滤字段
        $newDatas = ArrayUtility::arrayFilterKeys($datas, array_values($searchFields));
        //去重数据
        $newDatas = array_unique($newDatas, SORT_REGULAR);
        foreach ($newDatas as $dataKey => $dataField) {
            foreach ($searchFields as $fieldKey => $fieldsValue) {
                if ($dataField[$fieldsValue]) {
                    $newDatas[$dataKey][$fieldKey] = $dataField[$fieldsValue];
                }
            }
        }
        // 实例化请求类
        $res = null;
        $remoteService = new RemoteService(RemoteService::REQUEST_WAY_CURL);
        $remoteService->setIsIgnoreErr($isIgnoreErr);
        $remoteService->getInstance();
        $url = BaseBaseservice::getBaseserviceUrl('/pc/basics/' . $dataName . '/search');
        $params = ['body' => json_encode($newDatas)];
        $res = $remoteService->request('POST', $url, $params);
        return $res ? $res[$dataName . '_list'] : [];
    }


}