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
}
