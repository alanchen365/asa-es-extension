<?php

namespace AsaEs\Cache;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Exception\AppException;
use AsaEs\Exception\Service\RedisException;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Swoole\ServerManager;
use think\validate\ValidateRule;

class EsRedis extends \Redis
{
    private static $dbInstance;

    /**
     * 构建redis
     */
    public function __construct($host=null, $port = null, $password=null, $select=null, $timeout=null, $expire=null, $persistent=null, $prefix=null)
    {
        try {
            if (empty($host) || empty($port)) {
                throw new RedisException(5003);
            }

            if ($persistent) {
                $this->pconnect($host, $port, $timeout, 'persistent_id_'.$select);
            } else {
                $this->connect($host, $port, $timeout);
            }

            if ('' != $password) {
                $this->auth($password);
            }

            if (0 != $select) {
                $this->select($select);
            }

        } catch (\Exception $e) {
            throw new RedisException(5002);
        }
    }

    /**
     * 获取model的rediskey
     */
    public static function getBeanKeyPre(string $key, string $type):string
    {
        return strtoupper(AppInfo::APP_EN_NAME ."_".AppInfo::APP_VERSION . "_" . $key."_".$type);
    }

    /**
     * 获取基础的redis Key
     */
    public static function getKeyPre($key)
    {
        if (is_array($key)) {
            return array_map(function ($val) {
                return self::getKeyPre($val);
            }, $key);
        }
        return strtoupper(AppInfo::APP_EN_NAME ."_".AppInfo::APP_VERSION . "_" . $key);
    }

    /*
     * 根据keys 批量删除
     */
    public function delAll(?array $keys)
    {
        if (empty($keys)) {
            return ;
        }

        $pipe = $this->multi(\Redis::PIPELINE);
        foreach ($keys as $key) {
            $pipe->del($key);
        }
        $pipe->exec();
    }
}
