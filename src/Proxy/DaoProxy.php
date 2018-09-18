<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午12:54
 */
namespace AsaEs\Proxy;

use AsaEs\Base\BaseDao;
use AsaEs\Logger\FileLogger;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use EasySwoole\Core\Swoole\ServerManager;
use think\validate\ValidateRule;

class DaoProxy
{
    protected $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class): void
    {
        $this->class = $class;
    }

    public function __call($actionName, $arguments)
    {
        if ($this->class instanceof BaseDao) {

            // 方法是否存在
            $ref = new \ReflectionClass($this->class);
            if ($ref->hasMethod($actionName) &&  $ref->getMethod($actionName)->isPublic() && !$ref->getMethod($actionName)->isStatic()) {

                /**
                 * get by id
                 */
                if ($actionName == 'getById') {

                    // 如果开启了缓存
                    if ($this->class->isAutoCache()) {

                        // 缓存是否命中
                        $cacheObj = call_user_func_array([$this->class,'getByIdCache'], $arguments);
                        $cacheId = $cacheObj->getId() ?? null;

                        if (isset($cacheId)) {
                            return $cacheObj;
                        }

                        // 如果数据已被删除
                        if (call_user_func_array([$this->class,'basicIsDeleted'], $arguments)) {
                            return (object)[];
                        }
                    }
                    
                    // 走数据库
                    $dbObj = call_user_func_array([$this->class,'getById'], $arguments);
                    $dbId = method_exists($dbObj,'getId') ? $dbObj->getId() : null;

                    if ($this->class->isAutoCache()) {
                        // 保存缓存
                        if (isset($dbId)) {
                            call_user_func_array([$this->class,'setByIdCache'], [$dbId,$dbObj->toArray()]);
                        }
                    }

                    return $dbObj;
                }

                /**
                 * get all
                 */
                if ($actionName == 'getAll'){

                    if ($this->class->isAutoCache()) {

                    }

                    // 走数据库

                    if ($this->class->isAutoCache()){

                    }
                }

                /**
                 * search all
                 */
                if ($actionName == 'getById'){

                }


                return call_user_func_array([$this->class,$actionName], $arguments);
            }
        }
    }
}
