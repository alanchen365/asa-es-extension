<?php

namespace AsaEs\Utility;

use AsaEs\AsaEsConst;
use AsaEs\Cache\EsRedis;
use AsaEs\Config;

/**
 * 运维工具箱
 * Class Devops
 * @package AsaEs\Utility
 */
class Devops
{
    /**
     * clearRdisKeys
     * 获取基础数据操作redis key
     */
    public static function getModuleAllRedisKeys(string $tableName) :array
    {
        $keys = [];
        $keys[] =  EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_TYPE);
        $keys[] =  EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_GET_ALL);
        $keys[] =  EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_SEARCH_ALL);

        return $keys;
    }

    /**
    * 清除某个表所有的redis缓存
    */
    public static function clearModuleAllRedisCache($redisObj, string $tableName)
    {
        $keys = Devops::getModuleAllRedisKeys($tableName);
        foreach ($keys as $key) {
            $redisObj->delAll($redisObj->keys($key."*"));
        }
        
        return  Devops::getModuleAllRedisKeys($tableName);
    }

    /**
     * 清除某个表部分的redis缓存
     */
    public static function clearModuleAllRedisCacheById($redisObj, string $tableName, array $ids)
    {
        $key =  EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_TYPE);
        $keys = $redisObj->keys($key."*");

        $pipe = $redisObj->multi(\Redis::PIPELINE);
        foreach ($ids as $id) {
            $redisObj->del($key."_{$id}");
        }

        $redisObj->del(EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_GET_ALL));
        $redisObj->del(EsRedis::getBeanKeyPre($tableName, AsaEsConst::REDIS_BASIC_SEARCH_ALL));

        $pipe->exec();
        return  Devops::getModuleAllRedisKeys($tableName);
    }

    /**
     * 自定义缓存查看
     */
    public static function getRedisCacheByKey($redisObj, array $searchKeys)
    {
        $nKeys = [];
        foreach ($searchKeys as $key) {
            $nKeys[$key] = $redisObj->keys($key."*");
        }

        return $nKeys;
    }

    /**
     * 自定义缓存删除
     */
    public static function clearRedisCacheByKey($redisObj, array $keys)
    {
        $redisObj->delAll($keys);
    }
}
