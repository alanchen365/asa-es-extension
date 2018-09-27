<?php

namespace AsaEs\Cache;

use App\AppConst\AppInfo;
use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Exception\Service\RedisException;
use EasySwoole\Core\Component\Di;
use think\validate\ValidateRule;

class EsRedis
{
    private static $dbInstance;

    protected $options = [
        'select' => 0,
        'timeout' => 0,
        'expire' => 0,
        'persistent' => false,
        'prefix' => '',
    ];

    protected $dbType;

    public function __construct(string $dbType = AsaEsConst::DI_REDIS_DEFAULT)
    {
        $this->dbType = $dbType;
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

    /**
     * 链接redis
     */
    private function connect() :\Redis
    {
        try {
            $redisObj = Di::getInstance()->get($this->dbType);
            $redisConfog = Config::getInstance()->getConf('redis', true);
            $this->options = array_merge($this->options, $redisConfog[$this->dbType]);

            if ($this->options['persistent']) {
                $redisObj->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_'.$this->options['select']);
            } else {
                $redisObj->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
            }

            if ('' != $this->options['password']) {
                $redisObj->auth($this->options['password']);
            }

            if (0 != $this->options['select']) {
                $redisObj->select($this->options['select']);
            }

            return $redisObj;
        } catch (\RedisException $e) {
            throw new RedisException($e->getCode(), $e->getMessage());
        }
    }

    public function __call($actionName, $arguments)
    {
        self::$dbInstance = self::$dbInstance ?? $this->connect();
        if (self::$dbInstance instanceof \Redis) {
            $ref = new \ReflectionClass(\Redis::class);
            if ($ref->hasMethod($actionName) &&  $ref->getMethod($actionName)->isPublic()) {
                $this->__hook($actionName, $arguments);
                return call_user_func_array([self::$dbInstance,$actionName], $arguments);
            }
        }
    }

    private function __hook($actionName, $arguments)
    {
    }
}
