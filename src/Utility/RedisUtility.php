<?php

namespace AsaEs\Utility;

use AsaEs\AsaEsConst;
use AsaEs\Cache\EsRedis;
use EasySwoole\Core\Component\Di;

class RedisUtility
{
    /**
     * 清理模块缓存
     */
    public static function clearModuleCache(string $tableName, ?array $ids = [])
    {
        // redis 对象
        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);

        // redis-key
        $redisKeyBasic = EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_TYPE);
        $redisKeyGetAll =  EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_GET_ALL);
        $redisKeySearchAll =  EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_SEARCH_ALL);

        // 清理列表缓存
        $redisObj->del($redisKeyGetAll);
        $redisObj->del($redisKeySearchAll);

        // 清理单条缓存
        $pipe = $redisObj->multi(\Redis::PIPELINE);
        foreach ($ids as $key => $id) {
            $pipe->hDel($redisKeyBasic, $id);
        }
        $pipe->exec();
    }

    /**
     * 清理基础缓存 慎用
     * @param string $tableName
     */
    public static function clearBasicCache(string $tableName){

        $redisObj = Di::getInstance()->get(AsaEsConst::DI_REDIS_DEFAULT);
        $redisKeyBasic = EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_TYPE);
        $redisObj->del($redisKeyBasic);
    }
}
